<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\Skills\Skill;
use Maatwebsite\Excel\Concerns\ToModel;

class CourseSkillsImport implements ToModel
{


    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return Skill::find($row[0]);
    }

}
