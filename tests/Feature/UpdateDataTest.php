<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateDataTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;


    public function setUp()
    {
        parent::setUp();
        $this->seed('DatabaseSeeder');
        $this->student = factory(Student::class)->create();
    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set to 1, they are redirected to the relevant update screen
     * They may review and update their account basic data
     */
    public function SelectedUsersWillUpdateAccountData()
    {
        $student = factory(Student::class)->create();
        \Auth::guard(backpack_guard_name())->login($student->user);

        // check redirect to relevant update step
        $student->update(['force_update' => 1]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.account.info'));

        // check that the user can update their data
        $response = $this->get(route('backpack.account.info')); // user data edition
        $response->assertStatus(200);
        $response->assertSee($student->email);
        $response->assertSee($student->firstname);
        $response->assertSee($student->lastname);

        // check that the updated data is saved
        $firstname = $this->faker->firstName();
        $lastname = $this->faker->lastName();
        $email = $this->faker->unique()->safeEmail;

        $this->json('POST', route('backpack.account.info'), [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            ]);

        $this->assertEquals($student->firstname, $firstname);
        $this->assertEquals($student->lastname, $lastname);
        $this->assertEquals($student->email, $email);

        // check that the update process is moved to the next step
        $this->assertEquals(2, \Auth::guard(backpack_guard_name())->user()->student->force_update);
    }


    /**
     * @test
     * 
     * When a user has a force_update DB record set to 2, they are redirected to the relevant update screen
     * They may review and update their student-specific data
     */
    public function SelectedUsersWillUpdateStudentData()
    {
        $student = factory(Student::class)->create();
        \Auth::guard(backpack_guard_name())->login($student->user);
        
        // redirect
        $student->update(['force_update' => 2]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.student.info'));

        // review data
        $response = $this->get(route('backpack.student.info'));
        $response->assertStatus(200);
        $response->assertSee($student->birthdate);
        $response->assertSee($student->address);
        $response->assertSee($student->idnumber);
        
        // save updated data
        $address = $this->faker->address();
        $idnumber = $this->faker->randomNumber();
        $birthdate = $this->faker->date();

        $this->json('POST', route('backpack.student.info'), [
            'address' => $address,
            'idnumber' => $idnumber,
            'birthdate' => $birthdate,
            ]);

        $student = \Auth::guard(backpack_guard_name())->user()->student;

        $this->assertEquals($student->address, $address);
        $this->assertEquals($student->idnumber, $idnumber);
        $this->assertEquals($student->birthdate, $birthdate);

        // move update process to the next step
        $this->assertEquals(3, \Auth::guard(backpack_guard_name())->user()->student->force_update);

    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set to 3, they are redirected to the relevant update screen
     * (phone number)
     */
    public function SelectedUsersAreRedirectedToPhoneUpdateScreen()
    {
        \Auth::guard(backpack_guard_name())->login($student->user);
        
        $student->update(['force_update' => 3]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.account.phone'));
    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set to 4, they are redirected to the relevant update screen
     * (profession)
     */
    public function SelectedUsersAreRedirectedToProfessionUpdateScreen()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        
        $this->student->update(['force_update' => 4]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.account.profession'));
    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set to 5, they are redirected to the relevant update screen
     * (profile picture)
     */
    public function SelectedUsersAreRedirectedToPhotoUpdateScreen()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        
        $this->student->update(['force_update' => 5]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.account.photo'));
    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set to 6, they are redirected to the relevant update screen
     * (profile picture)
     */
    public function SelectedUsersAreRedirectedToContactsUpdateScreen()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        
        $this->student->update(['force_update' => 6]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.account.contacts'));
    }


    // todo add data validation testing
    


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
