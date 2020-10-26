<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Course;
use App\Models\GradeType;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\GradeController
 */
class GradeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->setSharedVariables();
        $this->seed('TestSeeder');
    }

    /** @test */
    public function add_grade_type_to_course_creates_grades_for_students()
    {
        $this->logAdmin();
        // A given course
        $course = factory(Course::class)->create();
        // with one enrolled student
        $student = factory(Student::class)->create();
        $student->enroll($course);
        $gradeType = factory(GradeType::class)->create();
        // has no grade types
        $this->assertEmpty($course->grade_type);
        // and no grades for the student
        $this->assertEmpty($course->grades);

        // after adding a grade type
        $response = $this->post('course/gradetype', [
            'course_id' => $course->id,
            'grade_type_id' => $gradeType->id,
        ]);
        $course->refresh();

        $response->assertRedirect();
        // the grade type is listed
        $this->assertNotEmpty($course->grade_type);
        // and the student has a default grade
        $this->assertEquals($course->grades->first()->student_id, $student->id);
    }

    /** @test */
    public function grades_can_be_edited()
    {
        $this->logAdmin();
        $grade = factory(\App\Models\Grade::class)->create([
            'grade' => 5,
        ]);

        $response = $this->post('grades', [
            'pk' => $grade->id,
            'value' => 7,
        ]);

        $response->assertOk();
        $this->assertSame($grade->fresh()->grade, 7);
    }

    /** @test */
    public function remove_grade_type_from_course_returns_an_ok_response()
    {
        $this->logAdmin();
        $course = factory(Course::class)->create();
        $grade = factory(\App\Models\Grade::class)->create([
            'course_id' => $course->id,
        ]);
        $this->assertNotEmpty($course->grades);

        $response = $this->delete(
            route(
                'removeGradeTypeFromCourse',
                [
                    'course' => $grade->course_id,
                    'gradetype' => $grade->grade_type_id,
                ]
            )
        );

        $response->assertOk();
        $this->assertEmpty($course->fresh()->grades);
    }
}
