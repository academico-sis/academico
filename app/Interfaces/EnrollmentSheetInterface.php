<?php

namespace App\Interfaces;

use App\Models\Enrollment;

interface EnrollmentSheetInterface
{
    public function exportToWord(Enrollment $enrollment);
}
