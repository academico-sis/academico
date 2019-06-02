<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use App\Models\Period;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getCourses()
    {
        return Course::get_courses_offer(Period::get_default_period())->toJson();
    }


}
