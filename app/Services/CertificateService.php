<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\GradeType;
use App\Models\Skills\Skill;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Localizable;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Certificates and result sheets as PDF files, in the institution's language.
 */
class CertificateService
{
    use Localizable;

    /**
     * Placeholders that can be used in the certificate text.
     *
     * @var list<string>
     */
    public const PLACEHOLDERS = ['{student}', '{course}', '{level}', '{period}', '{hours}', '{result}', '{start_date}', '{end_date}'];

    public function certificate(Enrollment $enrollment): StreamedResponse
    {
        return $this->withLocale(config('academico.locale'), fn (): StreamedResponse => $this->download(
            $this->pdf('pdf.certificate', [
                ...$this->header(),
                'title' => config('certificates.title') ?: __('Certificate of completion'),
                'body' => $this->certificateBody($enrollment),
                'place' => $this->placeAndDate(),
                'signerName' => config('certificates.signer_name'),
                'signerFunction' => config('certificates.signer_function'),
                'signature' => $this->privateImage(config('certificates.signature')),
                'background' => $this->privateImage(config('certificates.background')),
            ], 'L'),
            __('Certificate').' '.$enrollment->student_name,
        ));
    }

    public function result(Enrollment $enrollment): StreamedResponse
    {
        $course = $enrollment->course;
        $gradeTypes = $this->gradeTypes($course);

        return $this->withLocale(config('academico.locale'), fn (): StreamedResponse => $this->download(
            $this->pdf('pdf.result', [
                ...$this->header(),
                'enrollment' => $enrollment,
                'course' => $course,
                'gradeCategories' => $this->gradeCategories($enrollment, $gradeTypes),
                'grandTotal' => $this->total($enrollment, $gradeTypes),
                'skillTypes' => $this->skillTypes($enrollment),
                'place' => $this->placeAndDate(),
            ], 'P'),
            __('Result').' '.$enrollment->student_name,
        ));
    }

    public function courseResults(Course $course): StreamedResponse
    {
        $gradeTypes = $this->gradeTypes($course);

        $enrollments = $course->enrollments()
            ->whereDoesntHave('childrenEnrollments')
            ->with(['student.user', 'grades', 'result.result_name'])
            ->get()
            ->sortBy('student_name', SORT_NATURAL | SORT_FLAG_CASE);

        return $this->withLocale(config('academico.locale'), fn (): StreamedResponse => $this->download(
            $this->pdf('pdf.course-results', [
                ...$this->header(),
                'course' => $course,
                'gradeTypes' => $gradeTypes,
                'rows' => $enrollments->map(fn (Enrollment $enrollment): array => [
                    'student' => $enrollment->student_name,
                    'grades' => $gradeTypes->mapWithKeys(fn (GradeType $gradeType): array => [
                        $gradeType->id => $this->formatNumber($enrollment->grades->firstWhere('grade_type_id', $gradeType->id)?->grade),
                    ])->all(),
                    'total' => $this->total($enrollment, $gradeTypes),
                    'result' => $enrollment->result?->result_name?->name,
                ])->values()->all(),
                'place' => $this->placeAndDate(),
            ], 'L'),
            __('Results').' '.$course->name,
        ));
    }

    /**
     * The certificate text, escaped, with the placeholders replaced by the enrollment details.
     */
    private function certificateBody(Enrollment $enrollment): HtmlString
    {
        $course = $enrollment->course;

        $values = array_combine(self::PLACEHOLDERS, [
            $enrollment->student_name,
            $course->name,
            $course->level?->name,
            $course->period?->name,
            $this->formatNumber($course->total_volume),
            $enrollment->result?->result_name?->name,
            $this->formatDate($course->start_date),
            $this->formatDate($course->end_date),
        ]);

        $body = config('certificates.body') ?: __('This certifies that {student} has completed the course {course}, a total of {hours} hours.');

        return new HtmlString(nl2br(strtr(e($body), array_map(fn (?string $value): string => '<strong>'.e($value).'</strong>', $values))));
    }

    /**
     * @return array{institution: string, logo: ?string}
     */
    private function header(): array
    {
        $logo = config('academico.logo');

        return [
            'institution' => config('academico.company_name'),
            'logo' => $logo && Storage::disk('public')->exists($logo) ? Storage::disk('public')->path($logo) : null,
        ];
    }

    /**
     * @return Collection<int, GradeType>
     */
    private function gradeTypes(Course $course): Collection
    {
        return $course->evaluationType ? $course->gradeTypes()->get() : new Collection;
    }

    /**
     * The grades of the enrollment, grouped by category, with a subtotal per category.
     *
     * @param  Collection<int, GradeType>  $gradeTypes
     * @return list<array{name: ?string, grades: list<array{name: string, grade: ?string, total: string}>, grade: string, total: string}>
     */
    private function gradeCategories(Enrollment $enrollment, Collection $gradeTypes): array
    {
        return $gradeTypes
            ->groupBy(fn (GradeType $gradeType): string => $gradeType->category->name ?? '')
            ->map(fn (Collection $categoryGradeTypes, string $category): array => [
                'name' => $category ?: null,
                'grades' => $categoryGradeTypes->map(fn (GradeType $gradeType): array => [
                    'name' => $gradeType->name,
                    'grade' => $this->formatNumber($enrollment->grades->firstWhere('grade_type_id', $gradeType->id)?->grade),
                    'total' => $this->formatNumber($gradeType->total ?? 100),
                ])->values()->all(),
                ...$this->total($enrollment, $categoryGradeTypes),
            ])
            ->values()
            ->all();
    }

    /**
     * The sum of the enrollment's grades for these grade types, and the maximum it can reach.
     *
     * @param  Collection<int, GradeType>  $gradeTypes
     * @return array{grade: string, total: string}
     */
    private function total(Enrollment $enrollment, Collection $gradeTypes): array
    {
        return [
            'grade' => $this->formatNumber($enrollment->grades->whereIn('grade_type_id', $gradeTypes->pluck('id'))->sum('grade')),
            'total' => $this->formatNumber($gradeTypes->sum(fn (GradeType $gradeType): float => $gradeType->total ?? 100)),
        ];
    }

    /**
     * The skill evaluations of the enrollment, grouped by skill type.
     *
     * @return list<array{name: ?string, skills: list<array{name: string, scale: ?string}>}>
     */
    private function skillTypes(Enrollment $enrollment): array
    {
        $skills = $enrollment->course->skills()?->get() ?? new Collection;

        return $skills
            ->groupBy(fn (Skill $skill): string => $skill->skillType->name ?? '')
            ->map(fn (Collection $typeSkills, string $type): array => [
                'name' => $type ?: null,
                'skills' => $typeSkills->map(fn (Skill $skill): array => [
                    'name' => $skill->name,
                    'scale' => $enrollment->skillEvaluations->firstWhere('skill_id', $skill->id)?->skill_scale?->name,
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }

    private function placeAndDate(): string
    {
        $date = $this->formatDate(Carbon::now(config('settings.courses_timezone')));
        $city = config('certificates.city');

        return $city ? __(':city, :date', ['city' => $city, 'date' => $date]) : $date;
    }

    private function formatDate(?Carbon $date): ?string
    {
        return $date?->locale(app()->getLocale())->isoFormat('LL');
    }

    private function formatNumber(int|float|string|null $number): ?string
    {
        return $number === null ? null : Number::format((float) $number, maxPrecision: 2, locale: app()->getLocale());
    }

    /**
     * Absolute path of an image stored on the local disk, if it exists.
     */
    private function privateImage(?string $path): ?string
    {
        return $path && Storage::disk('local')->exists($path) ? Storage::disk('local')->path($path) : null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function pdf(string $view, array $data, string $orientation): string
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => $orientation,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'tempDir' => storage_path('framework/cache/mpdf'),
        ]);

        $mpdf->WriteHTML(view($view, $data)->render());

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    private function download(string $pdf, string $name): StreamedResponse
    {
        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf;
        }, Str::slug($name).'.pdf', ['Content-Type' => 'application/pdf']);
    }
}
