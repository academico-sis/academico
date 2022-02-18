<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Period;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GenderReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setSharedVariables();
        $this->seed('TestSeeder');
    }

    public function testStudentsGenderIsReflectedInReport()
    {
        $period = Period::get_default_period();

        $this->prepareData($period);

        $this->assertEquals(1, $period->studentCount(1));
        $this->assertEquals(1, $period->studentCount(2));
        $this->assertEquals(3, $period->studentCount(0));
    }

    public function test_view_returns_an_ok_response()
    {
        $user = factory(User::class)->create();
        $user->assignRole('admin');
        Auth::guard(backpack_guard_name())->login($user);

        $period = Period::get_default_period();
        $otherPeriod = factory(Period::class)->create();
        $otherPeriodInSameYear = factory(Period::class)->create(['year_id' => $otherPeriod->year_id]);

        $this->prepareData(period: $otherPeriod, femaleStudents: 10, maleStudents: 10, unknownStudents: 50, studentsWithoutInfo: 30);
        $this->prepareData(period: $otherPeriodInSameYear, femaleStudents: 80, maleStudents: 20, unknownStudents: 0, studentsWithoutInfo: 0);

        $response = $this->get(route('genderReport'));

        $response->assertOk();
        $response->assertViewIs('reports.gender');

        // Initial period shall not have any students
        $response->assertViewHas('data', function ($data) use ($period) {
            $data = $data->values();

            return
                $data->first()['periods'][1]['period'] === $period->name &&
                $data->first()['periods'][1]['male'] === 0 &&
                $data->first()['periods'][1]['female'] === 0 &&
                $data->first()['periods'][1]['unknown'] === 0;
        });

        // Both periods shall have the correct ratio of male/female/unknown gender
        $response->assertViewHas('data', function ($data) use ($otherPeriodInSameYear, $otherPeriod) {
            $periods = collect($data->values()[1]['periods']);

            $otherPeriodKey = $periods->where('period', $otherPeriod->name)->keys()->first();
            $otherPeriodInSameYearKey = $periods->where('period', $otherPeriodInSameYear->name)->keys()->first();

            return
                $periods[$otherPeriodKey]['period'] == $otherPeriod->name &&
                $periods[$otherPeriodKey]['male'] === 10 &&
                $periods[$otherPeriodKey]['female'] === 10 &&
                $periods[$otherPeriodKey]['unknown'] === 80 &&

                $periods[$otherPeriodInSameYearKey]['period'] == $otherPeriodInSameYear->name &&
                $periods[$otherPeriodInSameYearKey]['male'] === 20 &&
                $periods[$otherPeriodInSameYearKey]['female'] === 80 &&
                $periods[$otherPeriodInSameYearKey]['unknown'] === 0;
        });

        // The year shall have the correct total for both periods
        $response->assertViewHas('data', function ($data) use ($otherPeriodInSameYear, $otherPeriod) {
            $year = collect($data->values()[1]);

            return
                $year['year'] == $otherPeriod->year->name &&
                $year['male'] === 15 &&
                $year['female'] === 45 &&
                $year['unknown'] === 40;
        });
    }

    private function prepareData($period, $femaleStudents = 1, $maleStudents = 1, $unknownStudents = 2, $studentsWithoutInfo = 1): void
    {
        $course = factory(Course::class)->create(['period_id' => $period->id]);

        for ($i = 0; $i < $femaleStudents; $i++) {
            factory(Student::class)->create(['gender_id' => 1])->enroll($course);
        }
        for ($i = 0; $i < $maleStudents; $i++) {
            factory(Student::class)->create(['gender_id' => 2])->enroll($course);
        }
        for ($i = 0; $i < $unknownStudents; $i++) {
            factory(Student::class)->create(['gender_id' => 0])->enroll($course);
        }
        for ($i = 0; $i < $studentsWithoutInfo; $i++) {
            factory(Student::class)->create(['gender_id' => null])->enroll($course);
        }
    }
}
