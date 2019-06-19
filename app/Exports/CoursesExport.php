<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;

class CoursesExport implements FromCollection
{

    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->course->skills;
    }
}
