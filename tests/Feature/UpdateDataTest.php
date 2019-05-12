<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateDataTest extends TestCase
{

    use RefreshDatabase;


    public function setUp()
    {
        parent::setUp();
        $this->seed('DatabaseSeeder');
        $this->student = factory(Student::class)->create();
    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set, they are redirected to the update screen corresponding to the specified update step
     */
    public function SelectedUsersAreRedirectedToRelevantUpdateScreen()
    {

    }


    /**
     * Users may view the edit screen for basic data
     * This is Update Step 1
     * @test
     */
    public function UsersMayUpdateBasicData()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        $response = $this->get(route('backpack.account.info')); // user data edition
        $response->assertStatus(200);
        $response->assertSee($this->student->email);
        $response->assertSee($this->student->firstname);
        $response->assertSee($this->student->lastname);
    }

    // todo duplicate method ( edit // update) and test data saving to DB
    // todo add validation testing
    // todo test that the force update status is updated after each step.
    

    /**
     * Users may view the edit screen for additional data
     * This is Update Step 2
     * @test
     */
    public function UsersMayUpdateAdditionalData()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        $response = $this->get(route('backpack.student.info')); // student data edition
        $response->assertStatus(200);
        $response->assertSee($this->student->birthdate);
        $response->assertSee($this->student->address);
        $response->assertSee($this->student->idnumber);
    }

    /**
     * Users may view the edit screen for phone numbers
     * This is Update Step 3
     * @test
     */
    public function UsersMayUpdatePhoneNumbers()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        $response = $this->get(route('backpack.account.info'));
        $response->assertStatus(200);
        // todo: test that the phone numbers are visible
        // todo refactor post method.
    }

    /**
     * Users may update their profile picture
     * This is Update Step 4
     * @test
     */
    public function UsersMayUpdateProfilePicture()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        $response = $this->get(route('backpack.account.info'));
        $response->assertStatus(200);
        $response->assertSee();
    }

    /**
     * Users may view the edit screen for profession and institution
     * This is Update Step 5
     * @test
     */
    public function UsersMayUpdateWorkData()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        $response = $this->get(route('backpack.account.info'));
        $response->assertStatus(200);
        $response->assertSee();
    }

    /**
     * Users may view the edit screen for additional contacts
     * This is Update Step 6
     * @test
     */
    public function UsersMayUpdateAdditionalContacts()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        $response = $this->get(route('backpack.account.info'));
        $response->assertStatus(200);
        $response->assertSee();
    }
}
