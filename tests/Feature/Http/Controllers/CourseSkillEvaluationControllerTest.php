<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Skills\Skill;
use App\Models\Skills\SkillScale;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CourseSkillEvaluationController
 */
class CourseSkillEvaluationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->setSharedVariables();
        $this->seed('TestSeeder');
    }

    /** @test */
    public function edit_returns_an_ok_response()
    {
        $this->logAdmin();
        $course = factory(\App\Models\Course::class)->create();
        $student = factory(\App\Models\Student::class)->create();
        $student->enroll($course);

        $response = $this->get(route('studentSkillsEvaluation', [$course, $student]));

        $response->assertOk();
        $response->assertViewIs('skills.student');
        $response->assertViewHas('course');
        $response->assertViewHas('student');
        $response->assertViewHas('skills');
        $response->assertViewHas('skillScales');
        $response->assertViewHas('result');
        $response->assertViewHas('enrollment');
        $response->assertViewHas('results');
    }

    /** @test */
    public function index_returns_an_ok_response()
    {
        $this->logAdmin();
        $course = factory(\App\Models\Course::class)->create();

        $response = $this->get(route('courseSkillsEvaluation', [$course]));

        $response->assertOk();
        $response->assertViewIs('skills.students');
        $response->assertViewHas('course');
        $response->assertViewHas('skills');
        $response->assertViewHas('skill_evaluations');
        $response->assertViewHas('enrollments');
    }

    /** @test */
    public function skills_can_be_evaluated()
    {
        $this->logAdmin();
        $course = factory(\App\Models\Course::class)->create();
        $skill = factory(Skill::class)->create();
        $student = factory(Student::class)->create();
        $course->skills()->attach($skill, ['weight' => 1]);
        $skillScale = factory(SkillScale::class)->create();
        $this->assertEmpty($course->skill_evaluations);

        $response = $this->post(route('storeSkillEvaluation'), [
            'skill' => $skill->id,
            'status' => $skillScale->id,
            'student' => $student->id,
            'course' => $course->id,
        ]);
        $course->refresh();

        $response->assertOk();
        $this->assertEquals($course->skill_evaluations->first()->skill_id, $skill->id);
        $this->assertEquals($course->skill_evaluations->first()->skill_scale_id, $skillScale->id);
    }
}
