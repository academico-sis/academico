<?php

namespace App\Services;

use App\Interfaces\CertificatesInterface;
use App\Models\Course;
use App\Models\Enrollment;

class GenericCertificatesService implements CertificatesInterface
{
    public function exportCertificate(Enrollment $enrollment)
    {
        abort(501, __('Certificate generation is not configured. Set CERTIFICATES_STYLE in your .env file.'));
    }

    public function exportCourseResults(Course $course)
    {
        abort(501, __('Certificate generation is not configured. Set CERTIFICATES_STYLE in your .env file.'));
    }

    public function exportResult(Enrollment $enrollment)
    {
        abort(501, __('Certificate generation is not configured. Set CERTIFICATES_STYLE in your .env file.'));
    }
}
