<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;

interface CertificatesService
{
    public function exportCertificate(Enrollment $enrollment);
    public function exportCourseResults(Course $course);
    public function exportResult(Enrollment $enrollment);
}
