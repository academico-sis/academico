<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\Profession;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 2, they are redirected to the relevant update screen
     * They may review and update their student-specific data
     */
    public function SelectedUsersWillUpdateStudentData()
    {
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 3, they are redirected to the relevant update screen
     * (phone number)
     */
    public function SelectedUsersWillUpdatePhoneNumbers()
    {
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 4, they are redirected to the relevant update screen
     * They may update their profession and institution and move to the next update step.
     */
    public function SelectedUsersWillUpdateProfession()
    {
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 5, they are redirected to the relevant update screen
     * (profile picture)
     */
    public function SelectedUsersWillUpdateProfilePicture()
    {
    }

    /**
     * @test
     *
     * When a user has a force_update DB record set to 6, they are redirected to the relevant update screen
     * (profile picture)
     */
    public function SelectedUsersWillUpdateContacts()
    {
    }
}
