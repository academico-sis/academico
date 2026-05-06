<?php

namespace App\Services;

use App\Interfaces\EnrollmentSheetInterface;
use App\Models\Enrollment;

class GenericEnrollmentSheetService implements EnrollmentSheetInterface
{
    public function exportToWord(Enrollment $enrollment)
    {
        abort(501, __('Enrollment sheet export is not configured. Set ENROLLMENT_SHEET_STYLE in your .env file.'));
    }
}
