<?php

namespace App\Imports;

use App\Models\Skills\Skill;
use Maatwebsite\Excel\Concerns\ToModel;

class CourseSkillsImport implements ToModel
{
    public function model(array $row)
    {
        return Skill::find($row[0]);
    }
}
