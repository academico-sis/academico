<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CourseSkillEvaluationController
 */
class CourseSkillEvaluationControllerTest extends TestCase
{
    /**
     * @test
     */
    public function edit_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $course = factory(\App\Models\Course::class)->create();
        $student = factory(\App\Models\Student::class)->create();

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

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $course = factory(\App\Models\Course::class)->create();

        $response = $this->get(route('courseSkillsEvaluation', [$course]));

        $response->assertOk();
        $response->assertViewIs('skills.students');
        $response->assertViewHas('course');
        $response->assertViewHas('skills');
        $response->assertViewHas('skill_evaluations');
        $response->assertViewHas('enrollments');

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->post(route('storeSkillEvaluation'), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
