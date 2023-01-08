<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateDataTest extends TestCase
{
    public $student;
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
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
        $this->markTestIncomplete('Test unfinished');
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 2, they are redirected to the relevant update screen
     * They may review and update their student-specific data
     */
    public function SelectedUsersWillUpdateStudentData()
    {
        $this->markTestIncomplete('Test unfinished');
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 3, they are redirected to the relevant update screen
     * (phone number)
     */
    public function SelectedUsersWillUpdatePhoneNumbers()
    {
        $this->markTestIncomplete('Test unfinished');
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 4, they are redirected to the relevant update screen
     * They may update their profession and institution and move to the next update step.
     */
    public function SelectedUsersWillUpdateProfession()
    {
        $this->markTestIncomplete('Test unfinished');
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 5, they are redirected to the relevant update screen
     * (profile picture)
     */
    public function SelectedUsersWillUpdateProfilePicture()
    {
        $this->markTestIncomplete('Test unfinished');
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 6, they are redirected to the relevant update screen
     * (profile picture)
     */
    public function SelectedUsersWillUpdateContacts()
    {
        $this->markTestIncomplete('Test unfinished');
    }
}
