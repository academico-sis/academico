<?php

namespace App\Listeners;

class ComputeStudentLeadStatus
{
    public function handle($event)
    {
        // Get the student and update the lead status id
        $student = $event->enrollment->student;
        $student->lead_type_id = $student->lead_status();
        $student->save();
    }
}
