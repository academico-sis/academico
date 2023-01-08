<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\CourseTime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Admin\CourseCrudController
 */
class CourseCrudControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
        $this->logAdmin();
    }

    /**
     * @test
     */
    public function create_returns_an_ok_response()
    {
        $response = $this->get(route('course.create'));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function destroy_returns_an_ok_response()
    {
        $course = factory(Course::class)->create();
        $response = $this->delete(route('course.destroy', ['id' => $course->id]));
        $response->assertOk();
        $this->assertModelMissing($course);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response()
    {
        $course = factory(Course::class)->create();
        $response = $this->get(route('course.edit', ['id' => $course->id]));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function course_times_can_be_added()
    {
        // given a course
        $course = factory(Course::class)->create();
        $user = User::first();
        $user->assignRole('admin');

        \Auth::guard(backpack_guard_name())->login($user);

        // initially, the course has no associated course times
        $this->assertTrue($course->times->count() == 0);

        // course times can be created
        $this->put(route('course.update', ['id' => $course->id]), [
            'times' => '[{"day":"3","start":"09:00:00","end":"11:00:00"}]',
        ]);

        // course time should be associated to the course
        $this->assertTrue($course->times()->where('day', 3)->where('start', '09:00:00')->where('end', '11:00:00')->count() > 0);

        // and events will be created
        $this->assertTrue($course->events->count() > 0);
        // todo add assertions
    }

    /**
     * @test
     */
    public function course_times_can_be_removed()
    {
        // given a course with 2 course times
        $course = factory(Course::class)->create();
        factory(CourseTime::class)->create(['course_id' => $course->id]);
        factory(CourseTime::class)->create(['course_id' => $course->id]);
        $user = User::first();
        $user->assignRole('admin');

        \Auth::guard(backpack_guard_name())->login($user);

        // initially, the course has 2 associated course times
        $this->assertTrue($course->times->count() == 2);

        // course times can be created
        $this->put(route('course.update', ['id' => $course->id]), [
            'times' => '[{"day":"3","start":"09:00:00","end":"11:00:00"}]',
        ]);

        // course time should be associated to the course
        $this->assertTrue($course->fresh()->times->count() == 1);
        $this->assertTrue($course->times()->where('day', 3)->where('start', '09:00:00')->where('end', '11:00:00')->count() == 1);

        // and events will be created
        $this->assertTrue($course->events->count() > 0);
        // todo add assertions
    }

    /**
     * @test
     */
    public function course_times_can_be_updated()
    {
        // given a course with associated course times
        $course = factory(Course::class)->create();
        $courseTime1 = factory(CourseTime::class)->create(['course_id' => $course->id]);
        $courseTime2 = factory(CourseTime::class)->create(['course_id' => $course->id]);
        $courseTime3 = factory(CourseTime::class)->make();
        $user = User::first();
        $user->assignRole('admin');

        \Auth::guard(backpack_guard_name())->login($user);

        // initially, the course has 2 associated course times
        $this->assertTrue($course->times->count() == 2);

        // if a course time is not changed, it remains untouched.
        $this->put(route('course.update', ['id' => $course->id]), [
            'times' => json_encode([
                ['day' => $courseTime1->day, 'start' => $courseTime1->start, 'end' => $courseTime1->end],
                ['day' => $courseTime3->day, 'start' => $courseTime3->start, 'end' => $courseTime3->end],
            ]),
        ]);

        $this->assertTrue($course->times->count() == 2);

        $this->assertEquals($courseTime1->id, $course->fresh()->times->first()->id);

        // other course times are removed
        $this->assertTrue($course->fresh()->times->where('day', $courseTime2->day)->where('start', $courseTime2->start)->where('end', $courseTime2->end)->count() == 0);

        // or created
        $this->assertTrue($course->fresh()->times->where('day', $courseTime3->day)->where('start', $courseTime3->start)->where('end', $courseTime3->end)->count() == 1);
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $response = $this->get(route('course.index'));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function search_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->post(route('course.search'), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->post(route('course.store'), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->put(route('course.update', ['id' => $id]), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
