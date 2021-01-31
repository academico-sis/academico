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
        var_dump('launching API');
        $response = Http::post(config('services.apolearn.url') . '/auth.gettoken', [
              'api_key' => $this->apiKey,
              'username' => config('services.apolearn.username'),
              'password' => config('services.apolearn.password'),
        ]);

        return $response['result'];
    }

    public function createUser(User $user) : void
    {
        var_dump('checking if user exists for local ID ' . $user->id);
        // first check if the user already exists (email)
        $response = Http::get(config('services.apolearn.url') . "/users/getbyemail/$user->email", [
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);

        // if the user exists, just update their ID
        if ($this->actionSucceeded($response)) {
            var_dump('user found, saving their LMS ID to our DB');
            $user->update(['lms_id' => $response->json()['result']['user']['id']]);
        } else {
            // otherwise create them
            var_dump('user not found, creating them on the remote API now');
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
                Log::error($response->json());
            }
        }
    }

    public function updateUser(User $user) : void
    {
        var_dump('Checking if local user ' . $user->id . ' exists on remote');

        // first check if the user already exists (email)
        $response = Http::get(config('services.apolearn.url') . "/users/getbyemail/$user->email", [
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);

        // if the user does not exist, abort
        if (!$this->actionSucceeded($response)) {
            abort(404, 'User not found on remote');
        }

        $userId = $response->json()['result']['user']['id'];

        $response = Http::put(config('services.apolearn.url') . '/users/' . $userId, [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'password' => $user->password,
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);
    }

    public function createCourse(Course $course) : void
    {
        // first check if the course already has an ID (meaning it exists on the remote lms)
        if ($course->lms_id) {
            abort(422, 'This course already exists on the remote platform');
        }

        var_dump('pushing local course ' . $course->id . ' to API');
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
        $this->addTeacher($course);
    }

    public function updateCourse(Course $course) : void
    {
        if (!$course->lms_id) {
            $this->createCourse($course);
        }

        var_dump('updating course with locale ID' . $course->id);
        $response = Http::put(config('services.apolearn.url') . "/classrooms/$course->lms_id", [
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

        // if the course has no teacher, stop
        if (!$course->teacher) {
            Log::alert('The course has no teacher on local system, aborting execution now');
            die();
        }

        var_dump('updating the course teacher');
        // ensure the teacher is up to date
        $response = Http::get(config('services.apolearn.url') . "/classrooms/teachers/$course->lms_id", [
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);

        if ($this->actionSucceeded($response)) {
            $teachers = collect($response->json()['result']['users']);
            var_dump('found these teachers IDs on LMS:' . implode(', ', $teachers->pluck('id')->toArray()));

            // check if remote course teachers are still valid
            foreach ($teachers as $teacher) {
                var_dump('comparing ' . $teacher['id'] . ' and ' . $course->teacher->user->lms_id);
                if ($teacher['id'] !== $course->teacher->user->lms_id) {
                    var_dump('Removing teacher ' . $teacher['id'] . ' from course');
                    $this->removeTeacher($course->lms_id, $teacher['id']);
                }
            }

            if (!$course->teacher->user->lms_id || !$teachers->contains('id', $course->teacher->user->lms_id)) {
                var_dump('the course teacher has changed, need to update it');
                $this->addTeacher($course);
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

    protected function addTeacher(Course $course) : void
    {
        var_dump('adding teacher');
        // if the course has no teacher, abort
        if (! $course->teacher_id) {
            abort (422, "The course has no teacher");
        }

        // if the teacher doesn't exist on LMS, create them
        if (! $course->teacher->user->lms_id) {
            var_dump('creating user now');
            $this->createUser($course->teacher->user);
        }

        // then sync to API
        var_dump('pushing course user to API');
        $response = Http::post(config('services.apolearn.url')."/classrooms/addteacher/$course->lms_id", [
            'user_id' => $course->teacher->user->lms_id,
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);
    }

    protected function removeTeacher($courseId, $teacherId): void
    {
        var_dump('Removing teacher ' . $teacherId . ' from course ' . $courseId);
        $response = Http::put(config('services.apolearn.url')."/classrooms/removeteacher/$courseId", [
            'user_id' => $teacherId,
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);

        var_dump('and removing them again as students');
        $this->removeStudent($courseId, $teacherId);
    }

    public function removeStudent($courseId, $userId): void
    {
        var_dump('removing user id ' . $userId . ' from course ' . $courseId);
        $response = Http::put(config('services.apolearn.url')."/classrooms/removestudent/$courseId", [
            'user_id' => $userId,
            'auth_token' => $this->token,
            'api_key' => $this->apiKey,
        ]);
    }
}
