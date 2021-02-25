<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\Grade;
use App\Models\GradeType;
use App\Models\Student;
use App\Models\Teacher;
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
        $this->markTestIncomplete('Needs refactor for new eval workflow');
        $this->logAdmin();
        // A given course
        $course = factory(Course::class)->create();
        // with one enrolled student
        $student = factory(Student::class)->create();
        $enrollment_id = $student->enroll($course);
        $gradeType = factory(GradeType::class)->create();
        // has no grade types
        $this->assertEquals(0, $course->grade_types()->count());
        // and no grades for the student
        $this->assertEmpty($course->grades);

        // after adding a grade type
        $response = $this->post('course/gradetype', [
            'course_id' => $course->id,
            'grade_type_id' => $gradeType->id,
        ]);
        $course->refresh();

//        $response->assertRedirect();
        // the grade type is listed
        $this->assertNotEmpty($course->grade_types());
    }

    /** @test */
    public function grades_edit_screen_is_available_to_admins()
    {
        $this->logAdmin();
        $course = factory(Course::class)->create();
        $course->evaluationType()->associate(EvaluationType::find(1));
        $response = $this->get(route('editCourseGrades', ['course' => $course->id]));
        $response->assertSeeText($course->name);
    }

    /** @test */
    public function grades_edit_screen_is_available_to_authorized_users()
    {
        $teacher = factory(Teacher::class)->create();
        \Auth::guard(backpack_guard_name())->login($teacher->user);
        $course = factory(Course::class)->create(['teacher_id' => $teacher->id]);
        $course->evaluationType()->associate(EvaluationType::find(1));
        $response = $this->get(route('editCourseGrades', ['course' => $course->id]));
        $response->assertSeeText($course->name);
    }

    /** @test */
    public function grades_edit_screen_is_not_available_to_guests()
    {
        $course = factory(Course::class)->create();
        $course->evaluationType()->associate(EvaluationType::find(1));
        $response = $this->get(route('editCourseGrades', ['course' => $course->id]));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function grades_edit_screen_is_not_available_to_unauthorized_users()
    {
        // given a teacher who is not the owner of the course
        $teacher = factory(Teacher::class)->create();
        \Auth::guard(backpack_guard_name())->login($teacher->user);

        $course = factory(Course::class)->create();
        $this->assertNotEquals($teacher->id, $course->teacher_id);
        $course->evaluationType()->associate(EvaluationType::find(1));
        $response = $this->get(route('editCourseGrades', ['course' => $course->id]));
        $response->assertStatus(403);
    }

    /** @test */
    public function gradetypes_can_be_added_to_course_by_authorized_users()
    {
        $teacher = factory(Teacher::class)->create();
        \Auth::guard(backpack_guard_name())->login($teacher->user);
        $course = factory(Course::class)->create(['teacher_id' => $teacher->id]);
        $course->evaluationType()->associate(EvaluationType::find(1));
        $gradetype1 = factory(GradeType::class)->create();
        $course->grade_types()->save($gradetype1);
        $response = $this->get(route('editCourseGrades', ['course' => $course->id]));
        $response->assertSeeText($gradetype1->name);
    }

    /** @test */
    public function grades_can_be_edited()
    {
        $this->logAdmin();
        $grade = factory(Grade::class)->create([
            'grade' => 5,
        ]);

        $response = $this->post('grades', [
            'enrollment_id' => $grade->enrollment_id,
            'grade_type_id' => $grade->grade_type_id,
            'value' => 7,
        ]);

        $response->assertOk();
        $this->assertEquals($grade->fresh()->grade, 7);
    }

    /** @test */
    public function remove_grade_type_from_course_returns_an_ok_response()
    {
        $this->markTestIncomplete('Needs refactor for new eval workflow');
        $this->logAdmin();
        $course = factory(Course::class)->create();
        $enrollment = factory(Enrollment::class)->create([
            'course_id' => $course->id,
        ]);
        $grade = factory(Grade::class)->create([
            'enrollment_id' => $enrollment->id,
        ]);
        $this->assertNotEmpty($enrollment->grades);

        $response = $this->delete(
            route(
                'removeGradeTypeFromCourse',
                [
                    'course' => $course->id,
                    'gradetype' => $grade->grade_type_id,
                ]
            )
        );

        $response->assertOk();
        $this->assertEmpty($enrollment->fresh()->grades);
    }
}
