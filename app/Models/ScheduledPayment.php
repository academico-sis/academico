<?php

namespace App\Models;

use App\Traits\ValueTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ScheduledPayment extends Model
{
    use HasFactory, LogsActivity;
    use ValueTrait;

    protected $table = 'scheduled_payments';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function scopeStatus(Builder $query, $status)
    {
        return match ($status) {
            '2' => $query->where('status', 2),
            '1' => $query->where('status', 1),
            default => $query,
        };
    }

    public function isPaid(): bool
    {
        return $this->status === 2;
    }

    public function markAsPaid()
    {
        $this->status = 2;
        $this->save();
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function invoiceDetails(): MorphMany
    {
        return $this->morphMany(InvoiceDetail::class, 'product');
    }

    public function invoices()
    {
        return $this->invoiceDetails->map(fn (InvoiceDetail $invoiceDetail) => $invoiceDetail->invoice)->filter();
    }

    public function getDateForHumansAttribute()
    {
        if ($this->date) {
            return Carbon::parse($this->date, 'UTC')->locale(App::getLocale())->isoFormat('LL');
        }

        return Carbon::parse($this->created_at, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }

    public function getStatusTypeNameAttribute()
    {
        return match ($this->status) {
            2 => __('Paid'),
            1 => __('Pending'),
            default => '-',
        };
    }
}
