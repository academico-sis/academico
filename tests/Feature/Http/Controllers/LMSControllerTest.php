<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\LMSController
 */
class LMSControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    /**
     * @test
     */
    public function localUsersAreCreatedOnRemoteLMS()
    {
        $user = factory(User::class)->create();

        Http::fake([
            "apolearnapi.com/users/getbyemail/$user->email" => Http::response(['result' => ['success' => false]], 200, ['Headers']),

            // Stub a string response for all other endpoints...
            'apolearnapi.com/users' => Http::response([
                "success" => true,
                "id" => 1,
                "firstname" => "string",
                "lastname" => "string",
                "email" => "string",
                "usertype" => "string",
                "usertype_id" => 0,
                "gender" => "male",
                "job" => "string",
                "company" => "string",
                "city" => "string",
                "country" => "string",
                "language" => "fr",
                "admin" => 0,
                "disabled" => 0,
                "url" => "string"], 200, ['Headers']),
        ]);

        $response = Http::get("apolearnapi.com/users/getbyemail/$user->email", [
            'auth_token' => config('services.apolearn.token'),
            'api_key' => config('services.apolearn.api_key'),
        ]);

        // the user should not be found and be created.
        $this->markTestIncomplete();

    }

    // test cases...
}
