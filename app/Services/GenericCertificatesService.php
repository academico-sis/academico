<?php

namespace App\Services;

use App\Interfaces\CertificatesInterface;
use App\Models\Course;
use App\Models\Enrollment;

class GenericCertificatesService implements CertificatesInterface
{
    public function exportCertificate(Enrollment $enrollment)
    {
        abort(403);
    }

    public function exportCourseResults(Course $course)
    {
        abort(403);
    }

    public function exportResult(Enrollment $enrollment)
    {
        abort(403);
    }
}
