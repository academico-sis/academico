<?php

namespace Tests\Unit;

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
