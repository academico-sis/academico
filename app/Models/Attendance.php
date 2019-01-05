<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public function student_data()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }

    public function get_absence_count(Period $period)
    {
        // get all enrollments for period
        // with attendance
        // count absences

        $students = $period->enrollments;

        // for each student, get attendance record

        
        
        // get all attendance for the period
        return $this
            ->where('attendance_type_id', 4)
            ->get();
        // group by student
        // count
    }

    public function get_pending_attendance()
    {

    }
}
