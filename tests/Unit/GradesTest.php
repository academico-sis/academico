<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeType;
use App\Models\GradeTypeCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestSeeder');
    }

    public function testCourseCanHaveGradeTypes()
    {
        // given a course
        $course = factory(Course::class)->create();

        // it is possible to attach one or several grade types to this course, ie. criteria to receive grades
        $gradeType1 = factory(GradeType::class)->create();
        $course->grade_types()->attach($gradeType1);
        $course->refresh();
        $this->assertTrue($course->grade_types->contains($gradeType1));

        $gradeType2 = factory(GradeType::class)->create();
        $course->grade_types()->attach($gradeType2);
        $course->refresh();
        $this->assertTrue($course->grade_types->contains($gradeType2));

        // the reverse should also be true
        $this->assertTrue($gradeType1->courses->contains($course));
        $this->assertTrue($gradeType2->courses->contains($course));
    }

    public function testGradesHaveCategoryNames()
    {
        $gradeCategory = factory(GradeTypeCategory::class)->create();
        $gradeType = factory(GradeType::class)->create(['grade_type_category_id' => $gradeCategory->id]);

        $grade = factory(Grade::class)->create([
            'grade_type_id' => $gradeType->id,
        ]);

        $this->assertEquals($grade->grade_type_category, $gradeCategory->name);
    }

    public function testGradesBelongToEnrollments()
    {
        $enrollment = factory(Enrollment::class)->create();
        $grade = factory(Grade::class)->create([
            'enrollment_id' => $enrollment->id,
        ]);
        $this->assertTrue($enrollment->grades->contains($grade->id));
        $this->assertEquals($grade->enrollment->id, $enrollment->id);
    }
}
