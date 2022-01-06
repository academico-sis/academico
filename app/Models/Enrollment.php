<?php

namespace App\Models;

use App\Events\EnrollmentCreated;
use App\Events\EnrollmentDeleted;
use App\Events\EnrollmentDeleting;
use App\Events\EnrollmentUpdated;
use App\Events\EnrollmentUpdating;
use App\Models\Skills\SkillEvaluation;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\Traits\LogsActivity;

class Enrollment extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];

    protected $appends = ['type', 'name', 'result_name', 'product_code', 'price', 'price_with_currency'];

    protected $with = ['student', 'course', 'childrenEnrollments'];

    protected static bool $logUnguarded = true;

    protected $dispatchesEvents = [
        'deleted' => EnrollmentDeleted::class,
        'deleting' => EnrollmentDeleting::class,
        'created' => EnrollmentCreated::class,
        'updating' => EnrollmentUpdating::class,
        'updated' => EnrollmentUpdated::class,
    ];

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
            ->whereIn('status_id', ['1', '2'])
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

    public function scopePeriod(Builder $query, int $periodId)
    {
        return $query->whereHas('course', function ($q) use ($periodId) {
            $q->where('period_id', $periodId);
        });
    }

    public function scopeCourse(Builder $query, int $courseId)
    {
        return $query->where('course_id', $courseId);
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

    public function isPaid()
    {
        return $this->status_id == 2;
    }

    public function markAsUnpaid()
    {
        $this->status_id = 1;
        $this->save();

        $this->invoices()->delete();

        // also mark children as unpaid
        foreach ($this->childrenEnrollments as $child) {
            $child->status_id = 1;
            $child->invoices()->delete();
            $child->save();
        }
    }

    public function addScholarship(Scholarship $scholarship)
    {
        $this->scholarships()->sync($scholarship);
        if (config('invoicing.adding_scholarship_marks_as_paid')) {
            $this->markAsPaid();
        }
    }

    public function removeScholarship($scholarship)
    {
        $this->scholarships()->detach($scholarship);
        if (config('invoicing.adding_scholarship_marks_as_paid')) {
            $this->markAsUnpaid();
        }
    }

    /** RELATIONS */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scholarships()
    {
        return $this->belongsToMany(Scholarship::class);
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

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function scheduledPayments()
    {
        return $this->hasMany(ScheduledPayment::class);
    }

    public function saveScheduledPayments($payments)
    {
        $this->scheduledPayments()->delete();
        foreach ($payments as $payment) {
            $this->scheduledPayments()->create([
                'date' => $payment->date,
                'value' => $payment->value,
                'status' => $payment->status,
            ]);
        }
    }

    /* Accessors */

    public function getResultNameAttribute()
    {
        return $this->result->result_name->name ?? '-';
    }

    public function skill_evaluations()
    {
        return $this->hasMany(SkillEvaluation::class);
    }

    public function getStudentNameAttribute()
    {
        return $this->student->name ?? '';
    }

    public function getNameAttribute()
    {
        return __('Enrollment for').' '.$this->student_name;
    }

    public function getTypeAttribute()
    {
        return 'enrollment';
    }

    /*     public function getStudentIdAttribute()
        {
            return $this->student['id'];
        } */

    public function getStudentAgeAttribute()
    {
        return $this->student->student_age;
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
        return Carbon::parse($this->created_at, 'UTC')->locale(App::getLocale())->isoFormat('LL');
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
        return $this->course->rhythm->product_code ?? ' ';
    }

    public function getAttendanceRatioAttribute()
    {
        $courseEventIds = $this->course->events->pluck('id');
        $attendances = $this->student->attendance()->with('event')->get()->whereIn('event_id', $courseEventIds);
        if ($attendances->count() > 0) {
            return round(100 * (($attendances->where('attendance_type_id', 1)->count() + $attendances->where('attendance_type_id', 2)->count() * 0.75) / $attendances->count()));
        } else {
            return;
        }
    }

    public function getAbsenceCountAttribute()
    {
        $courseEventIds = $this->course->events->pluck('id');
        $attendances = $this->student->attendance()->with('event')->get()->whereIn('event_id', $courseEventIds);

        return $attendances->where('attendance_type_id', 3)->count() + $attendances->where('attendance_type_id', 4)->count();
    }

    public function getPriceAttribute()
    {
        if ($this->total_price !== null) {
            return $this->total_price / 100;
        }

        // if enabled, retrieve the default price category for the student
        if (config('invoicing.price_categories_enabled') && $this->student?->price_category) {
            $price_category = $this->student->price_category;

            return $this->course->$price_category ?? 0;
        }

        // finally, we default to the course price or 0 (because some screens need a value here, it cannot be null)
        return $this->course->price ?? 0;
    }

    public function getPriceWithCurrencyAttribute()
    {
        if (config('app.currency_position') === 'before') {
            return config('app.currency_symbol').' '.$this->price;
        }

        return $this->price.' '.config('app.currency_symbol');
    }

    public function getBalanceAttribute()
    {
        $balance = $this->price;
        $paidAmount = 0;

        foreach ($this->invoices as $invoice) {
            $paidAmount += $invoice->payments?->sum('value') ?? 0;
        }

        $balance -= $paidAmount;

        return $balance;
    }

    public function cancel()
    {
        // if the enrollment had children, delete them entirely
        if ($this->childrenEnrollments->count() > 0) {
            foreach ($this->childrenEnrollments as $child) {
                $child->delete();
            }
        }

        // delete attendance records related to the enrollment
        $attendances = $this->course->attendance->where('student_id', $this->student->id);
        Attendance::destroy($attendances->map(fn ($item, $key) => $item->id));

        foreach ($this->course->children as $child) {
            $attendances = $child->attendance->where('student_id', $this->student->id);
            Attendance::destroy($attendances->map(fn ($item, $key) => $item->id));
        }

        $this->delete();
    }

    public function getTotalPaidPriceAttribute()
    {
        $total = 0;
        foreach ($this->invoices as $invoice) {
            $total += $invoice->total_price;
        }

        return $total;
    }

    public function setTotalPriceAttribute($value)
    {
        $this->attributes['total_price'] = $value * 100;
    }

    public function getHasBookForCourseAttribute()
    {
        if ($this->course->books->count() > 0) {
            foreach ($this->course->books as $book) {
                // if the student doesn't have one of the course books
                if ($this->student->books->where('id', $book->id)->count() == 0) {
                    return false;
                }

                // if one book is expired
                if ($this->student && $this->student->books->where('id', $book->id)->filter(fn ($book) => $book->pivot->expiry_date == null || $book->pivot->expiry_date > Carbon::now())->count() == 0) {
                    return 'EXP';
                }
            }

            return 'OK';
        }
    }
}
