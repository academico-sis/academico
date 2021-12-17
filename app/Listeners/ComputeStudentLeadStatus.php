<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ComputeStudentLeadStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //Get the student and update the lead status id
        $enrollment = $event->enrollment;
        $enrollment->student->lead_type_id = $enrollment->student->getLeadStatusAttribute();
        $enrollment->student->save();
    }
}
