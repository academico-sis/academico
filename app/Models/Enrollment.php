<?php

namespace App\Models;

use App\Models\Skills\SkillEvaluation;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Enrollment extends Model
{
    use CrudTrait;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = ['student_id', 'course_id', 'parent_id', 'status_id'];
    protected $append = ['childrenEnrollments', 'productCode'];
    protected $with = ['student', 'course'];
    protected static $logUnguarded = true;

    protected static function boot()
    {
        parent::boot();

        // when creating a new enrollment, also add past attendance
        static::created(function (self $enrollment) {
            $events = $enrollment->course->events->where('start', '<', (new Carbon)->toDateString());
            foreach ($events as $event) {
                $event->attendance()->create([
                    'student_id' => $enrollment->student_id,
                    'attendance_type_id' => 3,
                ]);
            }
        });
    }

    /**
     * return all pending enrollments, without the child enrollments.
     */
    public function scopeParent($query)
    {
        return $query
        ->where('parent_id', null)
        ->get();
    }

    public function scopeReal($query)
    {
        return $query
            ->whereDoesntHave('childrenEnrollments')
            ->get();
    }

    public function scopeWithoutChildren($query)
    {
        return $query
            ->where(function ($query) {
                $query->whereDoesntHave('childrenEnrollments')
                ->where('parent_id', null);
            })
            ->orWhere(function ($query) {
                $query->where('parent_id', null);
            })
            ->get();
    }

    /** only pending enrollments */
    public function scopePending($query)
    {
        return $query
            ->where('status_id', 1)
            ->where('parent_id', null)
            ->get();
    }

    public function scopeNoresult($query)
    {
        return $query->doesntHave('result');
    }

    public function scopePeriod(Builder $query, $period)
    {
        return $query->whereHas('course', function ($q) use ($period) {
            $q->where('period_id', $period);
        });
    }

    /** FUNCTIONS */
    public function changeCourse(Course $newCourse)
    {
        $this->course_id = $newCourse->id;
        $this->save();
    }

    public function markAsPaid()
    {
        $this->status_id = 2;
        $this->save();

        // also mark children as paid
        foreach ($this->childrenEnrollments as $child) {
            $child->status_id = 2;
            $child->save();
        }
    }

    /** RELATIONS */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function pre_invoice()
    {
        return $this->belongsToMany(PreInvoice::class, 'enrollment_pre_invoice', 'enrollment_id', 'pre_invoice_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function result()
    {
        return $this->hasOne(Result::class)
            ->with('result_name')
            ->with('comments');
    }

    public function childrenEnrollments()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function enrollmentStatus()
    {
        return $this->belongsTo(EnrollmentStatusType::class, 'status_id');
    }

    /* Accessors */
    public function getGradesAttribute()
    {
        return Grade::where('course_id', $this->course->id)
            ->where('student_id', $this->student->id)
            ->with('grade_type')
            ->get();
    }

    public function getResultNameAttribute()
    {
        return $this->result->result_name->name ?? '-';
    }

    public function getSkillsAttribute()
    {
        return SkillEvaluation::where('student_id', $this->student->id)
            ->where('course_id', $this->course->id)
            ->with('skill')->with('skill_scale')
            ->get();
    }

    public function getStudentNameAttribute()
    {
        return $this->student['name'];
    }

    /*     public function getStudentIdAttribute()
        {
            return $this->student['id'];
        } */

    public function getStudentAgeAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->student['birthdate'])->age;
    }

    public function getStudentBirthdateAttribute()
    {
        return $this->student->birthdate;
    }

    public function getStudentEmailAttribute()
    {
        return $this->student['email'];
    }

    public function getDateAttribute()
    {
        return Carbon::parse($this->created_at, 'UTC')->toFormattedDateString();
    }

    public function getChildrenCountAttribute()
    {
        return self::where('parent_id', $this->id)->count();
    }

    public function getChildrenAttribute()
    {
        return self::where('parent_id', $this->id)->with('course')->get();
    }

    public function getStatusAttribute()
    {
        return $this->enrollmentStatus->name;
    }

    public function getProductCodeAttribute()
    {
        return $this->course->rhythm->product_code;
    }

    public function getAttendanceRatioAttribute()
    {
        $courseEventIds = $this->course->events->pluck('id');
        $attendances = $this->student->attendance()->with('event')->get()->whereIn('event_id', $courseEventIds);
        if ($attendances->count() > 0) {
            return round(100 * (($attendances->where('attendance_type_id', 1)->count() + ($attendances->where('attendance_type_id', 2)->count() * 0.75)) / $attendances->count()));
        } else {
            return;
        }
    }

    public function getAbsenceCountAttribute()
    {
        $courseEventIds = $this->course->events->pluck('id');
        $attendances = $this->student->attendance()->with('event')->get()->whereIn('event_id', $courseEventIds);
        $absenceCount = $attendances->where('attendance_type_id', 3)->count() + $attendances->where('attendance_type_id', 4)->count();

        return $absenceCount;
    }

    public function cancel()
    {
        // if the enrollment had children, delete them entirely
        if ($this->childrenEnrollments && $this->childrenEnrollments->count() > 0) {
            foreach ($this->childrenEnrollments as $child) {
                $child->delete();
            }
        }

        // delete attendance records related to the enrollment
        $attendances = $this->course->attendance->where('student_id', $this->student->id);
        Attendance::destroy($attendances->map(function ($item, $key) {
            return $item->id;
        }));

        foreach ($this->course->children as $child) {
            $attendances = $child->attendance->where('student_id', $this->student->id);
            Attendance::destroy($attendances->map(function ($item, $key) {
                return $item->id;
            }));
        }

        $this->delete();
    }
}
