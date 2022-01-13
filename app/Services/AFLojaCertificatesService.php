<?php

namespace App\Services;

use App\Interfaces\CertificatesInterface;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class AFLojaCertificatesService implements CertificatesInterface
{
    public function __construct()
    {
        if (config('certificates-generation.style' !== 'afloja')) {
            abort(403);
        }
    }

    public function exportCertificate(Enrollment $enrollment)
    {
        if (Gate::forUser(backpack_user())->denies('view-enrollment', $enrollment)) {
            abort(403);
        }

        App::setLocale('es');

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf(['mode' => 'utf-8',
            'format' => 'a4',
            'orientation' => 'L',
            'margin_left' => 25,
            'margin_right' => 25,
            'margin_top' => 25,
            'margin_bottom' => 25,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir' => storage_path('temp'),
            'fontDir' => array_merge($fontDirs, [storage_path('afloja/fonts')]),
            'fontdata' => $fontData + ['bodoni' => ['R' => 'BOD_R.TTF',
                'B' => 'BOD_B.TTF', ],
                'frenchscript' => ['R' => 'FRSCRIPT.TTF'], ],
            'default_font' => 'bodoni', ]);

        $mpdf->WriteHTML(view('results.certificate', ['enrollment' => $enrollment])->render());
        $mpdf->Output();
    }

    public function exportCourseResults(Course $course)
    {
        if (Gate::forUser(backpack_user())->denies('view-course', $course)) {
            abort(403);
        }

        App::setLocale('es');

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf(['mode' => 'utf-8',
            'format' => 'a4',
            'orientation' => 'L',
            'margin_left' => 25,
            'margin_right' => 25,
            'margin_top' => 25,
            'margin_bottom' => 25,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir' => storage_path('temp'),
            'fontDir' => array_merge($fontDirs, [storage_path('afloja/fonts')]),
            'fontdata' => $fontData + ['calibri' => ['R' => 'calibri.ttf',
                'B' => 'calibrib.ttf', ]],
            'default_font' => 'calibri', ]);

        $mpdf->WriteHTML(view('results.course-export', ['enrollments' => $course->enrollments()->with('grades')->get(),
            'course' => $course, ])->render());
        $mpdf->Output();
    }

    public function exportResult(Enrollment $enrollment)
    {
        if (Gate::forUser(backpack_user())->denies('view-enrollment', $enrollment)) {
            abort(403);
        }

        App::setLocale('es');

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf(['mode' => 'utf-8',
            'format' => 'a4',
            'orientation' => 'P',
            'margin_left' => 25,
            'margin_right' => 25,
            'margin_top' => 25,
            'margin_bottom' => 25,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir' => storage_path('temp'),
            'fontDir' => array_merge($fontDirs, [storage_path('afloja/fonts')]),
            'fontdata' => $fontData + ['calibri' => ['R' => 'calibri.ttf',
                'B' => 'calibrib.ttf', ]],
            'default_font' => 'calibri', ]);

        $mpdf->WriteHTML(view('results.export', ['grades' => $enrollment->grades,
            'enrollment' => $enrollment, ])->render());
        $mpdf->Output();
    }
}
