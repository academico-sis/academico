<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Course;
use App\Models\Period;
use App\Models\Student;
use App\Models\Attendance;

use Illuminate\Http\Request;
use App\Models\AttendanceType;
use App\Traits\PeriodSelection;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{

public function get_event_attendance(Event $event)
{
    return $event->enrollments;
}


}
