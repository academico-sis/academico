<?php

namespace App\Models;

use App\Events\EnrollmentCreated;
use App\Events\EnrollmentDeleting;
use App\Events\EnrollmentUpdated;
use App\Events\EnrollmentUpdating;
use App\Models\Interfaces\InvoiceableModel;
use App\Models\Skills\SkillEvaluation;
use App\Traits\PriceTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperEnrollment
 */
class Enrollment extends Model implements InvoiceableModel
{
    use CrudTrait;
    use LogsActivity;

    public const ENROLLMENT_STATUSES_TO_COUNT_IN_STATS = ['1', '2'];

    protected $guarded = ['id'];

    protected $appends = ['type', 'name', 'result_name', 'product_code', 'price', 'price_with_currency'];

    protected $with = ['student', 'course', 'childrenEnrollments'];

    protected $dispatchesEvents = [
        'deleting' => EnrollmentDeleting::class,
        'created' => EnrollmentCreated::class,
        'updating' => EnrollmentUpdating::class,
        'updated' => EnrollmentUpdated::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

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
        return $this->status_id === 2;
    }

    public function markAsUnpaid()
    {
        $this->status_id = 1;
        $this->save();

        $this->invoiceDetails()->delete();

        // also mark children as unpaid
        foreach ($this->childrenEnrollments as $child) {
            $child->status_id = 1;
            $child->invoiceDetails()->delete();
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

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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

    public function invoiceDetails()
    {
        return $this->morphMany(InvoiceDetail::class, 'product');
    }

    public function invoices()
    {
        return $this->invoiceDetails->map(fn (InvoiceDetail $invoiceDetail) => $invoiceDetail->invoice)->filter();
    }

    // also includes invoices for this enrollment's scheduled payments.
    public function relatedInvoices()
    {
        $scheduledPaymentsInvoices = $this->scheduledPayments->map(fn (ScheduledPayment $scheduledPayment) => $scheduledPayment->invoices());

        return $this->invoices()->concat($scheduledPaymentsInvoices)->flatten(1);
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
        $paymentsToDelete = $this->scheduledPayments()->pluck('id')->diff($payments->pluck('id'));
        ScheduledPayment::whereIn('id', $paymentsToDelete)->delete();

        foreach ($payments as $payment) {
            $this->scheduledPayments()->updateOrCreate([
                'id' => $payment->id,
            ], [
                'date' => $payment->date,
                'value' => $payment->value,
                'status' => $payment->status,
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    public function getResultNameAttribute()
    {
        return $this->result->result_name->name ?? '-';
    }

    public function skillEvaluations()
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

    public function getTypeAttribute(): string
    {
        return 'enrollment';
    }

    public function getStudentAgeAttribute()
    {
        return $this->student->student_age;
    }

    public function getStudentBirthdateAttribute()
    {
        return $this->student->birthdate;
    }

    public function getStudentFormattedGenderAttribute()
    {
        return $this->student->formatted_gender;
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
        }
    }

    public function getAbsenceCountAttribute()
    {
        $courseEventIds = $this->course->events->pluck('id');
        $attendances = $this->student->attendance()->with('event')->get()->whereIn('event_id', $courseEventIds);

        return $attendances->where('attendance_type_id', 3)->count() + $attendances->where('attendance_type_id', 4)->count();
    }

    public function getPrice($value)
    {
        if ($value !== null) {
            return $value / 100;
        }

        // if enabled, retrieve the default price category for the student
        if (config('invoicing.price_categories_enabled') && $this->student?->price_category) {
            $price_category = $this->student->price_category;

            return $this->course->$price_category ?? 0;
        }

        // finally, we default to the course price or 0 (because some screens need a value here, it cannot be null)
        return $this->course->price ?? 0;
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
        foreach ($this->invoices() as $invoice) {
            $total += $invoice->paidTotal();
        }

        return $total;
    }

    public function totalPaidPrice()
    {
        $total = 0;
        foreach ($this->invoices() as $invoice) {
            $total += $invoice->paidTotal();
        }

        return $total;
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

    public function getBalanceAttribute()
    {
        if (! config('invoicing.invoices_contain_enrollments_only')) {
            abort(422, 'Configuration options forbid to access this value');
        }

        $balance = $this->total_price;
        foreach ($this->invoices() as $invoice) {
            $balance -= $invoice->paidTotal();
        }

        return $balance;
    }

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getPrice($value),
        );
    }

    public function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getPrice($value),
            set: fn ($value) => $value * 100,
        );
    }

    public function getPriceWithCurrencyAttribute(): string
    {
        if (config('academico.currency_position') === 'before') {
            return config('academico.currency_symbol').' '.$this->total_price;
        }

        return $this->total_price.' '.config('academico.currency_symbol');
    }
}
