<?php

namespace App\Models;

use App\Models\Teacher;
use App\Models\Leave;
use App\Models\LeaveType;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];
    protected $with = ['leaveType'];
    
    protected static function boot()
    {
        parent::boot();

        // do not save already existing leaves
        static::saving(function (Leave $leave) {
            if (Leave::where('teacher_id', $leave->teacher_id)->where('date', $leave->date)->count() > 0) {
                return false;
            }
        });

        // when a leave is, we detach the events from the teacher
        static::saved(function(Leave $leave) {
            $events = Event::where('teacher_id', $leave->teacher_id)->whereDate('start', $leave->date)->get();
            foreach($events as $event) {
                $event->teacher_id = null;
                $event->save();
            }
        });

    }

    public static function upcoming_leaves()
    {
        return Leave::limit(15)->get()->groupBy('teacher_id'); // todo return first teacher with date span
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
