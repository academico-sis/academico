<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\Skills\Skill;
use Maatwebsite\Excel\Concerns\ToModel;

class CourseSkillsImport implements ToModel
{

    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->course->skills()->attach(Skill::find($row[0]),
            ['weight' => 1]
        );
    }

}
