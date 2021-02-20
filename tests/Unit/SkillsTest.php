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
