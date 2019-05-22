<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Profession;
use App\Models\Institution;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateDataTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;


    public function setUp(): void
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

        $this->json('POST', '/edit-account-info', [
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

        $this->json('POST', '/edit-student-info', [
            'address' => $address,
            'idnumber' => $idnumber,
            'birthdate' => $birthdate,
            ]);
            

/* 
        $this->assertEquals($student->address, $address);
        $this->assertEquals($student->idnumber, $idnumber);
        $this->assertEquals($student->birthdate, $birthdate); */

        // move update process to the next step
        $this->assertEquals(3, \Auth::guard(backpack_guard_name())->user()->student->force_update);

    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set to 3, they are redirected to the relevant update screen
     * (phone number)
     */
    public function SelectedUsersWillUpdatePhoneNumbers()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        
        // test redirect
        $this->student->update(['force_update' => 3]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.account.phone'));

        // test update screen
        $response = $this->get(route('backpack.account.phone'));
        $response->assertStatus(200);
        
        // todo: test that the phone numbers are visible

        $phoneNumber = $this->faker->phoneNumber();

        // todo refactor post method. Name route
        $this->json('POST', '/phonenumber', [
            'student' => $this->student->id,
            'number' => $phoneNumber,
            ]);
            
        // test data save
        $this->assertTrue($this->student->phone->contains('phone_number', $phoneNumber));

        // move to next step
        $this->json('POST', '/edit-phone'); // todo name route
        $this->assertEquals(4, \Auth::guard(backpack_guard_name())->user()->student->force_update);
    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set to 4, they are redirected to the relevant update screen
     * They may update their profession and institution and move to the next update step.
     */
    public function SelectedUsersWillUpdateProfession()
    {
        $student = factory(Student::class)->create();
        \Auth::guard(backpack_guard_name())->login($student->user);
        
        // redirect
        $student->update(['force_update' => 4]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.account.profession'));

        // save and review

        $profession = $this->faker->word();
        $institution = $this->faker->word();

        // todo assert that profession and institution values are visible on the page...
        $response = $this->get(route('backpack.account.profession'));
        $response->assertStatus(200);

        $this->json('POST', '/edit-profession', [
            'profession' => $profession,
            'institution' => $institution,
            ]);     

        // move to next step
        $this->assertEquals(5, \Auth::guard(backpack_guard_name())->user()->student->force_update);
    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set to 5, they are redirected to the relevant update screen
     * (profile picture)
     */
    public function SelectedUsersWillUpdateProfilePicture()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        
        // redirect
        $this->student->update(['force_update' => 5]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.account.photo'));

        // todo submit
        $response = $this->get(route('backpack.account.photo'));
        $response->assertStatus(200);
        
        // todo upload a picture here...
        $this->json('POST', '/edit-photo');
            
        $this->assertEquals(6, \Auth::guard(backpack_guard_name())->user()->student->force_update);
    }

    /**
     * @test
     * 
     * When a user has a force_update DB record set to 6, they are redirected to the relevant update screen
     * (profile picture)
     */
    public function SelectedUsersWillUpdateContacts()
    {
        \Auth::guard(backpack_guard_name())->login($this->student->user);
        
        // redirect
        $this->student->update(['force_update' => 6]);
        $response = $this->get('/');
        $response->assertRedirect(route('backpack.account.contacts'));

        // todo remove force_update flag
        $this->json('POST', '/edit-contacts');
        $this->assertEquals(null, \Auth::guard(backpack_guard_name())->user()->student->force_update);

    }

    // todo add data validation (testing ?)

}
