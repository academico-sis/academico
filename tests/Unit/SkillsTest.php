<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillEvaluation;
use App\Models\Skills\SkillScale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestSeeder');
    }

    public function testCourseCanHaveSkills()
    {
        // given a course
        $course = factory(Course::class)->create();

        // it is possible to attach one or several skills to this course, ie. criteria to receive grades
        $skill1 = factory(Skill::class)->create();
        $course->skills()->attach($skill1);
        $course->refresh();
        $this->assertTrue($course->skills->contains($skill1));

        $skill2 = factory(Skill::class)->create();
        $course->skills()->attach($skill2);
        $course->refresh();
        $this->assertTrue($course->skills->contains($skill2));

        // the reverse should also be true
        $this->assertTrue($skill1->courses->contains($course));
        $this->assertTrue($skill2->courses->contains($course));
    }

    public function testSkillsHaveEvaluations()
    {
        $skill = factory(Skill::class)->create();

        $evaluation = factory(SkillEvaluation::class)->create([
            'skill_id' => $skill->id,
            'skill_scale_id' => 3,
        ]);

        $skill->skill_evaluations()->save($evaluation);

        $this->assertEquals($skill->skill_evaluations->first()->skill_scale_id, 3);

        $this->assertEquals($evaluation->skill->id, $skill->id);
    }

    public function testSkillEvaluationsBelongToEnrollments()
    {
        $enrollment = factory(Enrollment::class)->create();
        $skillEvaluation = factory(SkillEvaluation::class)->create(['enrollment_id' => $enrollment->id]);
        $this->assertEquals($skillEvaluation->enrollment->id, $enrollment->id);
    }

    public function testSkillEvaluationsHaveAScale()
    {
        $scale = factory(SkillScale::class)->create();
        $skillEvaluation = factory(SkillEvaluation::class)->create(['skill_scale_id' => $scale->id]);
        $this->assertEquals($skillEvaluation->skill_scale->id, $scale->id);
    }
}
