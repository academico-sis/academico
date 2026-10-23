<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Leave extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $with = ['leaveType'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    protected static function boot()
    {
        parent::boot();

        // a teacher has at most one leave per day
        static::saving(function (self $leave) {
            $isDuplicate = self::where('teacher_id', $leave->teacher_id)
                ->whereDate('date', $leave->date)
                ->when($leave->exists, fn ($query) => $query->whereKeyNot($leave->getKey()))
                ->exists();

            if ($isDuplicate) {
                return false;
            }
        });

        // the sessions of the teacher on that day wait for a substitute
        static::saved(function (self $leave) {
            if ($leave->wasChanged('date') && $leave->getOriginal('date')) {
                self::giveSessionsBackToTeacher($leave->teacher_id, $leave->getOriginal('date'));
            }

            $events = Event::where('teacher_id', $leave->teacher_id)->whereDate('start', $leave->date)->get();
            foreach ($events as $event) {
                $event->teacher_id = null;
                $event->save();
            }
        });

        static::deleted(fn (self $leave) => self::giveSessionsBackToTeacher($leave->teacher_id, $leave->date));
    }

    /** The unassigned sessions of the teacher's courses on that day are theirs again */
    private static function giveSessionsBackToTeacher(int $teacherId, mixed $date): void
    {
        Event::whereNull('teacher_id')
            ->whereDate('start', Carbon::parse($date)->toDateString())
            ->whereHas('course', fn ($query) => $query->where('teacher_id', $teacherId))
            ->update(['teacher_id' => $teacherId]);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
