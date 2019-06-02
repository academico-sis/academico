<?php

namespace App\Http\Api\Controllers;

use App\Models\Teacher;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttendanceController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getTeacherAttendance()
    {
        return Teacher::where('user_id', request()->user()->id)->firstOrFail()->events_with_pending_attendance;
    }


}
