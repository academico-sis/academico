<?php

namespace App\Models;

use App\Events\StudentDeleting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OverflowException;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Student extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    use LogsActivity;

    protected $dispatchesEvents = [
        'deleting' => StudentDeleting::class,
    ];

    public $timestamps = true;

    protected $guarded = [];

    public $incrementing = false;

    protected $with = ['user', 'phone', 'institution', 'profession'];

    protected $appends = ['email', 'name', 'firstname', 'lastname', 'student_age', 'student_birthdate', 'is_enrolled', 'formatted_gender'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function scopeEnrolled($query)
    {
        return $query->whereHas('enrollments', function ($q) {
            return $q->whereHas('course', function ($q) {
                return $q->where('period_id', Period::get_default_period()->id);
            });
        });
    }

    public function scopeNewInPeriod(Builder $query, int $periodId)
    {
        return $query->whereIn('id', Period::find($periodId)->newStudents()->pluck(['student_id'])->toArray());
    }

    /**
     * Photos of students, who are often minors, are kept on the private disk and shown through temporary signed URLs.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile-picture')->useDisk('local');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Max, 1200, 1200)
            ->optimize();
    }

    /**
     * Enroll the student in a course, and in the children courses (submodules) if any.
     * This is the single entry point for new enrollments: if the student is already enrolled
     * (pending or paid), the existing enrollment is returned instead of creating a duplicate.
     *
     * @throws OverflowException when the course has no spots left
     */
    public function enroll(Course $course): int
    {
        $existingEnrollment = $this->activeEnrollmentIn($course);

        if ($existingEnrollment) {
            return $existingEnrollment->id;
        }

        if (! $course->accepts_new_students) {
            throw new OverflowException(__('This course is full.'));
        }

        $enrollment = DB::transaction(function () use ($course): Enrollment {
            // the price is left empty so that it follows the course price and the student's price category
            $enrollment = Enrollment::create([
                'student_id' => $this->id,
                'course_id' => $course->id,
                'status_id' => 1,
                'responsible_id' => auth()->id() ?? 1,
            ]);

            foreach ($course->children as $childCourse) {
                Enrollment::firstOrCreate(
                    [
                        'student_id' => $this->id,
                        'course_id' => $childCourse->id,
                        'parent_id' => $enrollment->id,
                    ],
                    [
                        'responsible_id' => auth()->id() ?? 1,
                    ]
                );
            }

            return $enrollment;
        });

        return $enrollment->id;
    }

    /** Why the student cannot be enrolled in this course, or null if they can */
    public function getEnrollmentRefusalReason(Course $course): ?string
    {
        if ($this->activeEnrollmentIn($course)) {
            return __('The student is already enrolled in this course.');
        }

        if (! $course->accepts_new_students) {
            return __('This course is full.');
        }

        return null;
    }

    /** The pending or paid enrollment of the student in this course, if any */
    public function activeEnrollmentIn(Course $course): ?Enrollment
    {
        return Enrollment::where('student_id', $this->id)
            ->where('course_id', $course->id)
            ->whereIn('status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'student_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function phone(): MorphMany
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)
            ->with('course')
            ->orderByDesc('created_at');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)->withPivot('id', 'code', 'status_id', 'expiry_date');
    }

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    public function firstname(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? Str::title($this->user->firstname) : '',
        );
    }

    public function lastname(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? Str::upper($this->user->lastname) : '',
        );
    }

    public function email(): Attribute
    {
        return new Attribute(
            get: fn (): ?string => $this?->user?->email,
        );
    }

    public function name(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? "{$this->firstname} {$this->lastname}" : '',
        );
    }

    public function getStudentAgeAttribute()
    {
        return $this->birthdate ? Carbon::parse($this->birthdate)->age.' '.__('years old') : '';
    }

    public function getStudentBirthdateAttribute()
    {
        return $this->birthdate ? Carbon::parse($this->birthdate)->locale(App::getLocale())->isoFormat('LL') : '';
    }

    public function getIsEnrolledAttribute()
    {
        // if the student is currently enrolled
        if ($this->enrollments()->whereHas('course', fn ($q) => $q->where('period_id', Period::get_default_period()->id))->count() > 0) {
            return 1;
        }

        return false;
    }

    /**
     * URL of the profile picture. It expires after 30 minutes, unless the photo is still on a public disk.
     */
    public function getImageAttribute(): ?string
    {
        $media = $this->getMedia('profile-picture')->last();

        if (! $media) {
            return null;
        }

        $conversion = $media->hasGeneratedConversion('thumb') ? 'thumb' : '';

        return Storage::disk($media->disk)->providesTemporaryUrls()
            ? $media->getTemporaryUrl(now()->addMinutes(30), $conversion)
            : $media->getUrl($conversion);
    }

    public function getFormattedGenderAttribute(): string
    {
        return match ($this->gender_id) {
            1 => 'F',
            2 => 'M',
            default => '',
        };
    }
}
