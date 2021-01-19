<?php

namespace App\Services;

use App\Interfaces\LMSInterface;
use App\Models\Course;
use App\Models\Level;
use App\Models\Rhythm;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApolearnService implements LMSInterface
{
    public $apiKey;
    private $token;

    public function __construct()
    {
        $this->apiKey = config('services.apolearn.api_key');
        $this->token = $this->authenticate();
    }

    public function authenticate() : string
    {
        Log::info('launching API');
        $response = Http::post(config('services.apolearn.url') . '/auth.gettoken', [
              'api_key' => $this->apiKey,
              'username' => config('services.apolearn.username'),
              'password' => config('services.apolearn.password'),
        ]);

        return $response['result'];
    }

    public function createUser(User $user) : void
    {
        Log::info('checking if user exists');
        // first check if the user already exists (email)
        $response = Http::get(config('services.apolearn.url') . "/users/getbyemail/$user->email", [
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);

        // if the user exists, just update their ID
        if ($this->actionSucceeded($response)) {
            Log::info('user found, saving their ID');
            $user->update(['lms_id' => $response->json()['result']['user']['id']]);
        } else {
            // otherwise create them
            Log::info('user not found, creating them now');
            $response = Http::post(config('services.apolearn.url') . '/users', [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
                'auth_token' => $this->token,
                'api_key' => $this->apiKey,
            ]);
            if ($this->actionSucceeded($response)) {
                $user->update(['lms_id' => $response->json()['result']['id']]);
            } else {
                dd($response->json());
            }
        }
    }

    public function createCourse(Course $course) : void
    {
        Log::info('creating a new course');
        // first check if the course already has an ID (meaning it exists on the remote lms)
        if ($course->lms_id) {
            abort(422, 'This course already exists on the remove platform');
        }

        $response = Http::post(config('services.apolearn.url') . '/classrooms', [
            "name" => $course->name,
            "shortname" => $course->shortname,
            "description" => $course->description,
            "start_date" => strtotime($course->start_date),
            "end_date" => strtotime($course->end_date),
            "category_id" => $course->rhythm->lms_id ?? config('services.apolearn.default_category_id'),
            "level_id" => $course->level->lms_id ?? config('services.apolearn.default_level_id'),
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);

        $courseId = $response->json()['result']['id'];

        // store the course ID in our database
        $course->update(['lms_id' => $courseId]);

        // assign an admin to the new class
        $response = Http::post(config('services.apolearn.url') . "/classrooms/addadmin/$courseId", [
            'user_id' => config('services.apolearn.admin_user_id'),
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);

        // and a teacher
        $this->addTeacher($course, $courseId);
    }

    public function updateCourse(Course $course) : void
    {
        Log::info('updating a course');
        if (!$course->lms_id) {
            $this->createCourse($course);
        }

        /*$response = Http::put(config('services.apolearn.url') . "/classrooms/$course->lms_id", [
            "name" => $course->name,
            "shortname" => $course->shortname,
            "description" => $course->description,
            "start_date" => strtotime($course->start_date),
            "end_date" => strtotime($course->end_date),
            "category_id" => $course->rhythm->lms_id ?? config('services.apolearn.default_category_id'),
            "level_id" => $course->level->lms_id ?? config('services.apolearn.default_level_id'),
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);*/

        Log::info('updating the course teacher');
        // ensure the teacher is up to date
        $response = Http::get(config('services.apolearn.url') . "/classrooms/teachers/$course->lms_id", [
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);
        Log::info('found these teachers:');
        Log::info($response);
        if ($this->actionSucceeded($response)) {
            $teachers = collect($response->json()['result']['users']);
            if (!$teachers->contains('id', $course->teacher->lms_id)) {
                Log::info('the course teacher has changed, need to update it');
                foreach ($teachers as $teacher) {
                    Log::info('Removing teacher ' . $teacher['id'] . ' from course');
                    $this->removeTeacher($course->lms_id, $teacher['id']);
                }

                //$this->addTeacher($course, $course->lms_id);
            }
        }
    }

    public function enrollStudent(Course $course, Student $student): void
    {
        if (!$course->lms_id) {
            abort(404, 'This course is not synced with external LMS');
        }

        $courseId = $course->lms_id;

        // if the student is not synced with the LMS, create them
        if (!$student->user->lms_id) {
            $this->createUser($student->user);
        }

        $response = Http::post(config('services.apolearn.url') . "/classrooms/addstudent/$courseId", [
            'user_id' => $student->user->lms_id,
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);
    }

    protected function actionSucceeded(\Illuminate\Http\Client\Response $response): bool
    {
        return $response->ok() && array_key_exists('result', $response->json()) && $response->json()['result']['success'] == true;
    }

    protected function addTeacher(Course $course, $courseId): void
    {
        if ($course->teacher_id) {
            $this->createUser($course->teacher->user);
            $response = Http::post(config('services.apolearn.url')."/classrooms/addteacher/$courseId", [
                'user_id' => $course->teacher->user->lms_id,
                'auth_token' => $this->token,
                'api_key' => $this->apiKey,
            ]);
        }
    }

    protected function removeTeacher($courseId, $teacherId): void
    {
        $response = Http::post(config('services.apolearn.url')."/classrooms/removeteacher/$courseId", [
            'user_id' => $teacherId,
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);
    }
}
