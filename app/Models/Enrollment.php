<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Grade;
use App\Models\Course;
use App\Models\Result;
use App\Models\Comment;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\PreInvoice;
use Backpack\CRUD\CrudTrait;
use App\Models\EnrollmentStatusType;
use App\Models\Skills\SkillEvaluation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use CrudTrait;
    use SoftDeletes;

    protected $fillable = ['student_id', 'course_id', 'parent_id', 'status_id'];
    protected $append = ['childrenEnrollments'];

    protected static function boot()
    {
        parent::boot();

        // when creating a new enrollment, also add past attendance
        static::created(function(Enrollment $enrollment) {
            $events = $enrollment->course->events->where('start', '<', (new Carbon)->toDateString());
            foreach ($events as $event)
            {
                $event->attendance()->create([
                    'student_id' => $enrollment->student_id,
                    'attendance_type_id' => 3,
                ]);
            }
            
            
        });

    }

    /**
     * return all pending enrollments, without the child enrollments
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


    public function scopePeriod(Builder $query, $period) {
        return $query->whereHas('course', function ($q) use ($period) {
            $q->where('period_id', $period);
        });
    }

    
    /** FUNCTIONS */

    /** adds the enrollment to the user cart */
    public function addToCart()
    {
        $product = Cart::firstOrNew([
            'user_id' => $this->student->id,
            'product_id' => $this->id,
            'product_type' => Enrollment::class
        ]);

        $product->save();
        return $product->id;
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
        return $this->hasMany(Enrollment::class, 'parent_id');
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
        return $this->result->result_name->name ?? "-";
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
        return Enrollment::where('parent_id', $this->id)->count();
    }

    public function getChildrenAttribute()
    {
        return Enrollment::where('parent_id', $this->id)->with('course')->get();
    }

    public function getStatusAttribute()
    {
        return $this->enrollmentStatus->name;
    }

    
    public function cancel()
    {
        $this->status_id = 3; // cancelled
        $this->save();

        // if the enrollment had children, delete them entirely
         if ($this->childrenEnrollments && $this->childrenEnrollments->count() > 0)
        {
            foreach ($this->childrenEnrollments as $child)
            {
                $child->delete();
            }
        }

        // delete attendance records related to the enrollment
        $attendances = $this->course->attendance->where('student_id', $this->student->id);
        Attendance::destroy($attendances->map(function ($item, $key) {
            return $item->id;
        }));
    }
}
