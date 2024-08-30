<?php

namespace Tests\Unit;

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

    public function testSkillsHaveEvaluations(): void
    {
        $skill = factory(Skill::class)->create();

        $evaluation = factory(SkillEvaluation::class)->create([
            'skill_id' => $skill->id,
            'skill_scale_id' => 3,
        ]);

        $skill->skillEvaluations()->save($evaluation);

        $this->assertEquals($skill->skillEvaluations->first()->skill_scale_id, 3);

        $this->assertEquals($evaluation->skill->id, $skill->id);
    }

    public function testSkillEvaluationsBelongToEnrollments(): void
    {
        $enrollment = factory(Enrollment::class)->create();
        $skillEvaluation = factory(SkillEvaluation::class)->create(['enrollment_id' => $enrollment->id]);
        $this->assertEquals($skillEvaluation->enrollment->id, $enrollment->id);
    }

    public function testSkillEvaluationsHaveAScale(): void
    {
        $scale = factory(SkillScale::class)->create();
        $skillEvaluation = factory(SkillEvaluation::class)->create(['skill_scale_id' => $scale->id]);
        $this->assertEquals($skillEvaluation->skill_scale->id, $scale->id);
    }
}
