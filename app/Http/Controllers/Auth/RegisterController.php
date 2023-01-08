<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserCreated;
use App\Models\Contact;
use App\Models\Institution;
use App\Models\PhoneNumber;
use App\Models\Profession;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends \Backpack\CRUD\app\Http\Controllers\Auth\RegisterController
{
    public function checkEmailUnicity(Request $request)
    {
        if (User::where('email', $request->email)->count() !== 0) {
            abort(409);
        }

        return response('OK', 204);
    }

    protected function generateUsername($fullName): string
    {
        $username_parts = array_filter(explode(' ', strtolower($fullName)));
        $username_parts = array_slice($username_parts, -2);

        $part1 = (empty($username_parts[0])) ? '' : substr($username_parts[0], 0, 3);
        $part2 = (empty($username_parts[1])) ? '' : substr($username_parts[1], 0, 8);
        $part3 = random_int(999, 9999);

        //str_shuffle to randomly shuffle all characters

        return $part1.$part2.$part3;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();
        $users_table = $user->getTable();
        if (backpack_authentication_column() == 'email') {
            'email|';
        }

        return Validator::make($data, [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'username' => 'required|max:255|unique:'.$users_table,
            'gender' => 'numeric|in:0,1,2',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'rules' => 'required',
            'idnumber_type' => 'required',
            'idnumber' => 'required',
            'phonenumber' => 'required',
            'tc_consent' => 'required',
            'address' => 'required',
            'birthdate' => 'required',
            'profession' => 'required',
            'institution' => 'required',
            'userPicture' => 'image|nullable',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        Log::info('Starting student registration process');

        if (User::where('email', $data['email'])->count() === 0) {
            $username = $data['email'];
        } else {
            $username = $this->generateUsername($data['firstname'].' '.$data['lastname']);
        }

        // create the user
        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'username' => $username,
            'password' => bcrypt($data['password']),
        ]);

        Log::info('New user created with ID '.$user->id);

        UserCreated::dispatch($user, $data['password']);

        // create the student

        $student = Student::create([
            'id' => $user->id,
            'idnumber' => $data['idnumber'],
            'gender_id' => $data['gender'],
            'birthdate' => Carbon::parse($data['birthdate'])->toDateTimeString(),
            'address' => $data['address'],
        ]);

        Log::info('New student created with ID '.$student->id);

        return $student;
    }

    /**
     * Handle a registration request for the application.
     *
     *
     * @return Response
     */
    public function register(Request $request)
    {
        // if registration is closed, deny access
        if (! config('backpack.base.registration_open')) {
            abort(403, trans('backpack::base.registration_closed'));
        }

        $this->validator($request->data);
        $student = $this->create($request->data);

        // add phone number(s)

        foreach ($request->data['phonenumbers'] as $number) {
            PhoneNumber::create([
                'phoneable_id' => $student->id,
                'phoneable_type' => Student::class,
                'phone_number' => $number['number'],
            ]);
        }

        Log::info('Phone numbers added to the student profile');

        // add profession and institution
        if ($request->data['institution']) {
            $profession = Profession::firstOrCreate([
                'name' => $request->data['profession'],
            ]);

            $student->update([
                'profession_id' => $profession->id,
            ]);
        }

        if ($request->data['institution']) {
            $institution = Institution::firstOrCreate([
                'name' => $request->data['institution'],
            ]);

            $student->update([
                'institution_id' => $institution->id,
            ]);
        }

        Log::info('Profession and institution added to the student profile');

        // add photo
        if ($request->data['userPicture']) {
            $student
               ->addMediaFromBase64($request->data['userPicture'])
               ->usingFileName('profilePicture.jpg')
               ->toMediaCollection('profile-picture');

            Log::info('Profile picture added to the student profile');
        }

        // add contact(s)
        foreach ($request->data['contacts'] as $contact) {
            $newContact = Contact::create([
                'student_id' => $student->id,
                'firstname' => $contact['firstname'],
                'lastname' => $contact['lastname'],
                'idnumber' => $contact['idnumber'],
                'address' => $contact['address'],
                'email' => $contact['email'],
            ]);

            foreach ($contact['phonenumbers'] as $number) {
                PhoneNumber::create([
                    'phoneable_id' => $newContact->id,
                    'phoneable_type' => Contact::class,
                    'phone_number' => $number['number'],
                ]);
            }
        }

        Log::info('Aditional contacts associated to the student profile');

        return response(204);
    }
}
