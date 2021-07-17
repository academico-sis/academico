<?php

namespace App\Models;

use App\Events\LeadStatusUpdatedEvent;
use App\Events\StudentDeleted;
use App\Events\StudentDeleting;
use App\Events\StudentUpdated;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\App;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Student
 *
 * @property int $id
 * @property string $idnumber
 * @property string $address
 * @property string|null $zip_code
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property int|null $genre_id
 * @property string $birthdate
 * @property string|null $terms_accepted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $lead_type_id
 * @property string|null $how_did_you_know_us
 * @property int|null $force_update
 * @property int|null $profession_id
 * @property int|null $institution_id
 * @property string|null $account_holder
 * @property string|null $iban
 * @property string|null $bic
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Contact[] $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @property string $email
 * @property string $firstname
 * @property-read mixed $is_enrolled
 * @property string $lastname
 * @property-read mixed $lead_status
 * @property-read mixed $lead_status_name
 * @property-read string $name
 * @property-read mixed $student_age
 * @property-read mixed $student_birthdate
 * @property-read \App\Models\Institution|null $institution
 * @property-read \App\Models\LeadType|null $leadType
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PhoneNumber[] $phone
 * @property-read int|null $phone_count
 * @property-read \App\Models\Profession|null $profession
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $real_enrollments
 * @property-read int|null $real_enrollments_count
 * @property-read \App\Models\Title $title
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Student computedLeadType($leadTypeId)
 * @method static \Illuminate\Database\Eloquent\Builder|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereAccountHolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereBic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereForceUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereHowDidYouKnowUs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereIban($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereIdnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereLeadTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereProfessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereTermsAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereZipCode($value)
 * @mixin \Eloquent
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

    protected $fillable = [
        'id',
        'idnumber',
        'firstname',
        'lastname',
        'email',
        'address',
        'city',
        'state',
        'country',
        'title_id',
        'birthdate',
        'terms_accepted_at',
        'created_at',
        'updated_at',
        'lead_type_id',
        'force_update',
        'profession_id',
        'institution_id',
        'zip_code',
        'iban',
        'bic',
    ];

    public $incrementing = false;

    protected $with = ['user', 'phone', 'institution', 'profession', 'title'];

    protected $appends = ['email', 'name', 'firstname', 'lastname', 'student_age', 'student_birthdate', 'lead_status', 'is_enrolled'];

    protected static $logUnguarded = true;


    public function scopeComputedLeadType($query, $leadTypeId)
    {
        return match ($leadTypeId) {
            1 => $query->whereHas('enrollments', function ($query) {
                return $query->whereHas('course', function ($q) {
                    $q->where('period_id', Period::get_default_period()->id);
                });
            }),

            2, 3 => $query->where('lead_type_id', $leadTypeId),

            4 => $query
                ->where('lead_type_id', $leadTypeId)
                ->orWhere(function ($query) {
                    $query
                        ->whereNull('lead_type_id')
                        ->whereHas('enrollments', function ($query) {
                            return $query
                                ->whereHas('course', function ($q) {
                                    $q->where('period_id', '!=', Period::get_default_period()->id);
                                });
                        })
                        ->whereDoesntHave('enrollments', function ($query) {
                            return $query
                                ->whereHas('course', function ($q) {
                                    $q->where('period_id', Period::get_default_period()->id);
                                });
                        });
                }),

            default => $query,
        };
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_MAX, 1200, 1200)
            ->optimize();
    }

    /** relations */
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
        ->whereHas('event', function ($q) use ($period) {
            return $q->whereHas('course', function ($c) use ($period) {
                return $c->where('period_id', $period->id);
            });
        });
    }

    public function periodAttendance(Period $period = null)
    {
        if ($period == null) {
            $period = Period::get_default_period();
        }

        return $this->hasMany(Attendance::class)
        ->whereHas('event', function ($q) use ($period) {
            return $q->whereHas('course', function ($c) use ($period) {
                return $c->where('period_id', $period->id);
            });
        });
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
            ->with('course');
    }

    public function leadType()
    {
        return $this->belongsTo(LeadType::class);
    }

    public function real_enrollments()
    {
        return $this->hasMany(Enrollment::class)
            ->with('course')
            ->whereIn('status_id', ['1', '2'])
            ->whereDoesntHave('childrenEnrollments');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    /** attributes */
    public function getFirstnameAttribute(): string
    {
        if ($this->user) {
            return Str::title($this->user->firstname);
        }
        return '';
    }

    public function getLastnameAttribute(): string
    {
        if ($this->user) {
            return Str::upper($this->user->lastname);
        }
        return '';
    }

    public function getEmailAttribute(): string
    {
        if ($this->user) {
            return $this->user->email;
        }
        return '';
    }

    public function getNameAttribute(): string
    {
        if ($this->user) {
            return ($this->title ? ($this->title->title . ' ') : '') . $this->firstname.' '.$this->lastname;
        }
        return '';
    }

    public function getStudentAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age ?? '';
    }

    public function getStudentBirthdateAttribute()
    {
        return Carbon::parse($this->birthdate)->locale(App::getLocale())->isoFormat('LL');
    }

    public function getIsEnrolledAttribute()
    {
        // if the student is currently enrolled
        if ($this->enrollments()->whereHas('course', function ($q) {
            return $q->where('period_id', Period::get_default_period()->id);
        })->count() > 0) {
            return 1;
        }
    }

    public function getLeadStatusNameAttribute()
    {
        return LeadType::find($this->lead_status)->name;
    }

    public function getLeadStatusAttribute()
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
        elseif ($this->has('enrollments')) {
            return 4;
        } else {
            return;
        }
        // otherwise, their status cannot be determined and should be left blank
    }

    /** functions */

    /**
     * enroll the student in a course.
     * If the course has any children, we also enroll the student in the children courses.
     */
    public function enroll(Course $course): int
    {
        // avoid duplicates by retrieving an potential existing enrollment for the same course
        $enrollment = Enrollment::firstOrCreate([
            'student_id' =>  $this->id,
            'course_id' => $course->id,
        ],
        [
            'responsible_id' => backpack_user()->id ?? 1,
        ]);

        // if the course has children, enroll in children as well.
        if ($course->children_count > 0) {
            foreach ($course->children as $children_course) {
                Enrollment::firstOrCreate([
                    'student_id' =>  $this->id,
                    'course_id' => $children_course->id,
                    'parent_id' => $enrollment->id,
                ],
                [
                    'responsible_id' => backpack_user()->id ?? 1,
                ]);
            }
        }

        $this->update(['lead_type_id' => null]); // fallback to default (converted)

        // to subscribe the student to mailing lists and so on
        $listId = config('mailing-system.mailerlite.activeStudentsListId');
        LeadStatusUpdatedEvent::dispatch($this, $listId);

        foreach ($this->contacts as $contact)
        {
            LeadStatusUpdatedEvent::dispatch($contact, $listId);
        }

        return $enrollment->id;
    }

    /** SETTERS */
    public function setFirstnameAttribute($value)
    {
        $this->user->update(['firstname' => $value]);
    }

    public function setLastnameAttribute($value)
    {
        $this->user->update(['lastname' => $value]);
    }

    public function setEmailAttribute($value)
    {
        $this->user->update(['email' => $value]);
    }
}
