<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Course;

use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * get all events for a course
     */
    public function get_course_events($course)
    {
        $this->middleware(['permission:courses.view']);

        return Course::findOrFail($course)->events;
    }

}
