<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\Grade;
use App\Models\GradeType;
use App\Models\GradeTypeCategory;
use App\Models\Level;
use App\Models\Period;
use App\Models\Result;
use App\Models\ResultType;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillEvaluation;
use App\Models\Skills\SkillScale;
use App\Models\Skills\SkillType;
use App\Models\Student;
use App\Models\User;
use App\Services\CertificateService;
use App\Services\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class CertificateServiceTest extends TestCase
{
    use RefreshDatabase;

    private Course $course;

    private Enrollment $enrollment;

    /**
     * Data passed to the PDF views, by view name.
     *
     * @var array<string, array<string, mixed>>
     */
    private array $viewData = [];

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-23 10:00:00');

        View::composer('pdf.*', function (\Illuminate\View\View $view): void {
            $this->viewData[$view->name()] = $view->getData();
        });

        $this->course = Course::factory()->create([
            'name' => 'French A1',
            'level_id' => Level::factory()->create(['name' => 'A1'])->id,
            'period_id' => Period::factory()->create(['name' => 'Autumn 2026'])->id,
            'volume' => 30,
            'remote_volume' => 10.5,
            'start_date' => '2026-09-01',
            'end_date' => '2026-12-15',
        ]);

        $this->enrollment = $this->enroll('Jane', 'Doe');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_certificate_is_a_pdf_download(): void
    {
        $response = app(CertificateService::class)->certificate($this->enrollment);

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('certificate-jane-doe.pdf', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', TestResponse::fromBaseResponse($response)->streamedContent());
    }

    public function test_certificate_text_replaces_the_placeholders_and_escapes_html(): void
    {
        $this->giveResult($this->enrollment, 'PASS');

        app(Settings::class)->set([
            'certificates.title' => 'Diploma',
            'certificates.body' => "<b>{student}</b>\n{course} {level} {period} {hours} {result} {start_date} {end_date}",
        ]);

        app(CertificateService::class)->certificate($this->enrollment);

        $data = $this->viewData['pdf.certificate'];
        $this->assertSame('Diploma', $data['title']);
        $this->assertSame(
            '&lt;b&gt;<strong>Jane DOE</strong>&lt;/b&gt;<br />'."\n"
            .'<strong>French A1</strong> <strong>A1</strong> <strong>Autumn 2026</strong> <strong>40.5</strong> '
            .'<strong>PASS</strong> <strong>September 1, 2026</strong> <strong>December 15, 2026</strong>',
            (string) $data['body'],
        );
    }

    public function test_placeholder_values_are_escaped(): void
    {
        $this->enrollment->student->user->update(['firstname' => 'Tom & Jerry']);

        app(Settings::class)->set(['certificates.body' => '{student}']);

        app(CertificateService::class)->certificate($this->enrollment->fresh());

        $this->assertSame('<strong>Tom &amp; Jerry DOE</strong>', (string) $this->viewData['pdf.certificate']['body']);
    }

    public function test_certificate_uses_the_institution_language_and_restores_the_user_language(): void
    {
        app(Settings::class)->set([
            'academico.locale' => 'fr',
            'certificates.body' => '{hours} {start_date}',
            'certificates.city' => 'Lyon',
        ]);
        app()->setLocale('en');

        app(CertificateService::class)->certificate($this->enrollment);

        $data = $this->viewData['pdf.certificate'];
        $this->assertSame('Certificat de réussite', $data['title']);
        $this->assertSame('<strong>40,5</strong> <strong>1 septembre 2026</strong>', (string) $data['body']);
        $this->assertSame('Lyon, le 23 octobre 2026', $data['place']);
        $this->assertSame('en', app()->getLocale());
    }

    public function test_certificate_has_a_default_text(): void
    {
        app(CertificateService::class)->certificate($this->enrollment);

        $data = $this->viewData['pdf.certificate'];
        $this->assertSame('Certificate of completion', $data['title']);
        $this->assertSame(
            'This certifies that <strong>Jane DOE</strong> has completed the course <strong>French A1</strong>, a total of <strong>40.5</strong> hours.',
            (string) $data['body'],
        );
        $this->assertSame('October 23, 2026', $data['place']);
    }

    public function test_certificate_uses_the_uploaded_images(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $logo = UploadedFile::fake()->image('logo.png')->store('settings', 'public');
        $signature = UploadedFile::fake()->image('signature.png')->store('settings', 'local');
        $background = UploadedFile::fake()->image('background.jpg', 297, 210)->store('settings', 'local');

        app(Settings::class)->set([
            'academico.company_name' => 'Example School',
            'academico.logo' => $logo,
            'certificates.signer_name' => 'John Smith',
            'certificates.signer_function' => 'Director',
            'certificates.signature' => $signature,
            'certificates.background' => $background,
        ]);

        app(CertificateService::class)->certificate($this->enrollment);

        $data = $this->viewData['pdf.certificate'];
        $this->assertSame('Example School', $data['institution']);
        $this->assertSame(Storage::disk('public')->path($logo), $data['logo']);
        $this->assertSame(Storage::disk('local')->path($signature), $data['signature']);
        $this->assertSame(Storage::disk('local')->path($background), $data['background']);

        $html = view('pdf.certificate', $data)->render();
        $this->assertStringContainsString('John Smith', $html);
        $this->assertStringContainsString('Director', $html);
        $this->assertStringContainsString('background-image-resize', $html);
    }

    public function test_missing_images_are_left_out(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        app(Settings::class)->set([
            'academico.logo' => 'settings/deleted.png',
            'certificates.signature' => 'settings/deleted.png',
        ]);

        app(CertificateService::class)->certificate($this->enrollment);

        $data = $this->viewData['pdf.certificate'];
        $this->assertNull($data['logo']);
        $this->assertNull($data['signature']);
        $this->assertNull($data['background']);
        $this->assertStringNotContainsString('<img', view('pdf.certificate', $data)->render());
    }

    public function test_result_sheet_lists_grades_by_category_with_totals(): void
    {
        $oral = GradeTypeCategory::factory()->create(['name' => 'Oral']);
        $written = GradeTypeCategory::factory()->create(['name' => 'Written']);
        $speaking = GradeType::factory()->create(['name' => 'Speaking', 'total' => 20, 'grade_type_category_id' => $oral->id]);
        $listening = GradeType::factory()->create(['name' => 'Listening', 'total' => 20, 'grade_type_category_id' => $oral->id]);
        $essay = GradeType::factory()->create(['name' => 'Essay', 'total' => 60, 'grade_type_category_id' => $written->id]);
        $this->evaluateWith(gradeTypes: [$speaking, $listening, $essay]);

        Grade::factory()->create(['enrollment_id' => $this->enrollment->id, 'grade_type_id' => $speaking->id, 'grade' => 15.5]);
        Grade::factory()->create(['enrollment_id' => $this->enrollment->id, 'grade_type_id' => $essay->id, 'grade' => 42]);
        $result = $this->giveResult($this->enrollment, 'PASS');
        Comment::factory()->create([
            'commentable_type' => Result::class,
            'commentable_id' => $result->id,
            'body' => 'Good progress',
        ]);

        $response = app(CertificateService::class)->result($this->enrollment);

        $this->assertStringContainsString('result-jane-doe.pdf', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', TestResponse::fromBaseResponse($response)->streamedContent());

        $data = $this->viewData['pdf.result'];
        $this->assertSame([
            [
                'name' => 'Oral',
                'grades' => [
                    ['name' => 'Speaking', 'grade' => '15.5', 'total' => '20'],
                    ['name' => 'Listening', 'grade' => null, 'total' => '20'],
                ],
                'grade' => '15.5',
                'total' => '40',
            ],
            [
                'name' => 'Written',
                'grades' => [
                    ['name' => 'Essay', 'grade' => '42', 'total' => '60'],
                ],
                'grade' => '42',
                'total' => '60',
            ],
        ], $data['gradeCategories']);
        $this->assertSame(['grade' => '57.5', 'total' => '100'], $data['grandTotal']);

        $html = view('pdf.result', $data)->render();
        $this->assertStringContainsString('Jane DOE', $html);
        $this->assertStringContainsString('Speaking', $html);
        $this->assertStringContainsString('57.5 / 100', $html);
        $this->assertStringContainsString('PASS', $html);
        $this->assertStringContainsString('Good progress', $html);
    }

    public function test_result_sheet_lists_skill_evaluations(): void
    {
        $type = SkillType::factory()->create(['name' => 'Speaking']);
        $greet = Skill::factory()->create(['name' => 'Can greet someone', 'skill_type_id' => $type->id, 'order' => 1]);
        $introduce = Skill::factory()->create(['name' => 'Can introduce themselves', 'skill_type_id' => $type->id, 'order' => 2]);
        $this->evaluateWith(skills: [$greet, $introduce]);

        SkillEvaluation::factory()->create([
            'enrollment_id' => $this->enrollment->id,
            'skill_id' => $greet->id,
            'skill_scale_id' => SkillScale::factory()->create(['name' => ['en' => 'Acquired']])->id,
        ]);

        app(CertificateService::class)->result($this->enrollment);

        $data = $this->viewData['pdf.result'];
        $this->assertSame([], $data['gradeCategories']);
        $this->assertSame([
            [
                'name' => 'Speaking',
                'skills' => [
                    ['name' => 'Can greet someone', 'scale' => 'Acquired'],
                    ['name' => 'Can introduce themselves', 'scale' => null],
                ],
            ],
        ], $data['skillTypes']);
        $this->assertStringContainsString('Acquired', view('pdf.result', $data)->render());
    }

    public function test_course_without_evaluation_type_has_no_grades(): void
    {
        GradeType::factory()->create();

        app(CertificateService::class)->result($this->enrollment);

        $this->assertSame([], $this->viewData['pdf.result']['gradeCategories']);
        $this->assertSame([], $this->viewData['pdf.result']['skillTypes']);
    }

    public function test_course_results_list_the_students_in_alphabetical_order(): void
    {
        $oral = GradeType::factory()->create(['name' => 'Oral', 'total' => 50]);
        $written = GradeType::factory()->create(['name' => 'Written', 'total' => 50]);
        $this->evaluateWith(gradeTypes: [$oral, $written]);

        $adam = $this->enroll('Adam', 'Zola');
        $cancelled = $this->enroll('Carl', 'Cancelled');
        $cancelled->update(['status_id' => 3]);

        Grade::factory()->create(['enrollment_id' => $this->enrollment->id, 'grade_type_id' => $oral->id, 'grade' => 40]);
        Grade::factory()->create(['enrollment_id' => $this->enrollment->id, 'grade_type_id' => $written->id, 'grade' => 35]);
        Grade::factory()->create(['enrollment_id' => $adam->id, 'grade_type_id' => $oral->id, 'grade' => 20]);
        $this->giveResult($this->enrollment, 'PASS');

        $response = app(CertificateService::class)->courseResults($this->course);

        $this->assertStringContainsString('results-french-a1.pdf', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', TestResponse::fromBaseResponse($response)->streamedContent());

        $data = $this->viewData['pdf.course-results'];
        $this->assertSame([
            [
                'student' => 'Adam ZOLA',
                'grades' => [$oral->id => '20', $written->id => null],
                'total' => ['grade' => '20', 'total' => '100'],
                'result' => null,
            ],
            [
                'student' => 'Jane DOE',
                'grades' => [$oral->id => '40', $written->id => '35'],
                'total' => ['grade' => '75', 'total' => '100'],
                'result' => 'PASS',
            ],
        ], $data['rows']);

        $html = view('pdf.course-results', $data)->render();
        $this->assertStringContainsString('Adam ZOLA', $html);
        $this->assertStringContainsString('75 / 100', $html);
        $this->assertStringNotContainsString('Carl', $html);
    }

    private function enroll(string $firstname, string $lastname): Enrollment
    {
        $student = Student::factory()->create([
            'id' => User::factory()->create(['firstname' => $firstname, 'lastname' => $lastname])->id,
        ]);

        return Enrollment::factory()->create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
        ]);
    }

    private function giveResult(Enrollment $enrollment, string $name): Result
    {
        return Result::factory()->create([
            'enrollment_id' => $enrollment->id,
            'result_type_id' => ResultType::factory()->create(['name' => $name])->id,
        ]);
    }

    /**
     * @param  list<GradeType>  $gradeTypes
     * @param  list<Skill>  $skills
     */
    private function evaluateWith(array $gradeTypes = [], array $skills = []): void
    {
        $evaluationType = EvaluationType::factory()->create();
        $evaluationType->gradeTypes()->attach(collect($gradeTypes)->mapWithKeys(fn (GradeType $gradeType, int $order): array => [$gradeType->id => ['order' => $order]]));
        $evaluationType->skills()->attach(collect($skills)->mapWithKeys(fn (Skill $skill, int $order): array => [$skill->id => ['order' => $order]]));

        $this->course->update(['evaluation_type_id' => $evaluationType->id]);
        $this->course->refresh();
        $this->enrollment->refresh();
    }
}
