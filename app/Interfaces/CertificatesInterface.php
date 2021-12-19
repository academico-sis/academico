<?php

namespace App\Interfaces;

use App\Models\Course;
use App\Models\Enrollment;

interface CertificatesInterface
{
    public function exportCertificate(Enrollment $enrollment);

    public function exportCourseResults(Course $course);

    public function exportResult(Enrollment $enrollment);
}
