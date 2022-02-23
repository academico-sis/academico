<?php

namespace App\Models;

use App\Events\LeadStatusUpdatedEvent;
use App\Events\StudentDeleting;
use App\Events\StudentUpdated;
use App\Traits\UserAttributesTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperStudent
 */
class Student extends Model implements HasMedia
{
    use CrudTrait;
    use InteractsWithMedia;
    use LogsActivity;

    protected $dispatchesEvents = [
        'deleting' => StudentDeleting::class,
        'updated' => StudentUpdated::class,
    ];

    public $timestamps = true;

    protected $guarded = [];

    public $incrementing = false;

    protected $with = ['user', 'phone', 'institution', 'profession'];

    protected $appends = ['email', 'name', 'firstname', 'lastname', 'student_age', 'student_birthdate', 'is_enrolled'];

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

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_MAX, 1200, 1200)
            ->optimize();
    }

    /**
     * enroll the student in a course.
     * If the course has any children, we also enroll the student in the children courses.
     */
    public function enroll(Course $course): int
    {
        // avoid duplicates by retrieving an potential existing enrollment for the same course
        $enrollment = Enrollment::firstOrCreate(
            [
                'student_id' => $this->id,
                'course_id' => $course->id,
            ],
            [
                'responsible_id' => backpack_user()->id ?? 1,
            ]
        );

        // if the course has children, enroll in children as well.
        if ($course->children->count() > 0) {
            foreach ($course->children as $children_course) {
                Enrollment::firstOrCreate(
                    [
                        'student_id' => $this->id,
                        'course_id' => $children_course->id,
                        'parent_id' => $enrollment->id,
                    ],
                    [
                        'responsible_id' => backpack_user()->id ?? 1,
                    ]
                );
            }
        }

        $this->update(['lead_type_id' => null]); // fallback to default (converted)

        // to subscribe the student to mailing lists and so on
        $listId = config('mailing-system.mailerlite.activeStudentsListId');
        LeadStatusUpdatedEvent::dispatch($this, $listId);

        foreach ($this->contacts as $contact) {
            LeadStatusUpdatedEvent::dispatch($contact, $listId);
        }

        return $enrollment->id;
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function periodAbsences(Period $period = null)
    {
        if ($period == null) {
            $period = Period::get_default_period();
        }

        return $this->hasMany(Attendance::class)
        ->where('attendance_type_id', 4) // absence
        ->whereHas('event', fn ($q) => $q->whereHas('course', fn ($c) => $c->where('period_id', $period->id)));
    }

    public function periodAttendance(Period $period = null)
    {
        if ($period == null) {
            $period = Period::get_default_period();
        }

        return $this->hasMany(Attendance::class)
        ->whereHas('event', fn ($q) => $q->whereHas('course', fn ($c) => $c->where('period_id', $period->id)));
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'student_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function phone()
    {
        return $this->morphMany(PhoneNumber::class, 'phoneable');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class)
            ->with('course')->orderByDesc('course_id');
    }

    public function leadType()
    {
        return $this->belongsTo(LeadType::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function books()
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
            set: fn ($value) => $this->user->update(['firstname' => $value]),
        );
    }

    public function lastname(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? Str::title($this->user->lastname) : '',
            set: fn ($value) => $this->user->update(['lastname' => $value]),
        );
    }

    public function email(): Attribute
    {
        return new Attribute(
            get: fn (): ?string => $this?->user?->email,
            set: fn ($value) => $this->user->update(['email' => $value]),
        );
    }

    public function name(): Attribute
    {
        return new Attribute(
            get: fn (): string => $this->user ? "{$this->firstname} {$this->lastname}" : '',
            set: fn ($value) => $value * 100,
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

    public function getLeadStatusNameAttribute()
    {
        return LeadType::find($this->lead_type_id)->name ?? null;
    }

    public function computedLeadStatus()
    {
        // if the student is currently enrolled, they are CONVERTED
        if ($this->is_enrolled) {
            return 1;
        }

        // if the student has a special status, return it
        if ($this->leadType != null) {
            return $this->leadType->id;
        }

        // if the student was previously enrolled, they must be potential students
        if ($this->has('enrollments')) {
            return 4;
        }

        // otherwise, their status cannot be determined and should be left blank
        return null;
    }

    public function getImageAttribute(): ?string
    {
        return $this->getMedia('profile-picture')->last()?->getUrl('thumb');
    }

    public function setImageAttribute($value)
    {
        // if the image was erased
        if ($value == null) {
            $this->clearMediaCollection('profile-picture');
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image')) {
            $this->addMediaFromBase64($value)
                ->usingFileName('profilePicture.jpg')
                ->toMediaCollection('profile-picture');
        }
    }
}
