<?php

namespace App\Models;

use App\Models\Teacher;
use App\Models\Leave;
use App\Models\LeaveType;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];
    protected $with = ['leaveType'];
    
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
