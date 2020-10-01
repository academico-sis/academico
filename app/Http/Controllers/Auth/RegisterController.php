<?php

namespace App\Http\Controllers\Auth;

use App\Models\Contact;
use App\Models\Institution;
use App\Models\PhoneNumber;
use App\Models\Profession;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends \Backpack\CRUD\app\Http\Controllers\Auth\RegisterController
{
    public function checkEmailUnicity(Request $request)
    {
        if (User::where('email', $request->email)->count() == 0) {
            return response('OK', 204);
        } else {
            abort(409);
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();
        $users_table = $user->getTable();
        $email_validation = backpack_authentication_column() == 'email' ? 'email|' : '';

        return Validator::make($data, [
            'firstname'                            => 'required|max:255',
            'lastname'                             => 'required|max:255',
            backpack_authentication_column()       => 'required|'.$email_validation.'max:255|unique:'.$users_table,
            'password'                             => 'required|min:6',
            'rules'                                => 'required',
            'idnumber_type'                        => 'required',
            'idnumber'                             => 'required',
            'address'                              => 'required',
            'phonenumber'                          => 'required',
            'tc_consent'                           => 'required',
            'address'                              => 'required',
            'birthdate'                            => 'required',
            'profession'                           => 'required',
            'institution'                          => 'required',
            'userPicture'                          => 'required',
            function ($attribute, $value, $fail) {
                $size = strlen(base64_decode($value));

                if ($size > 3145728) {
                    $fail($attribute.' image too large');
                }

                $img = imagecreatefromstring($value);

                if (! $img) {
                    $fail($attribute.'Invalid image');
                }

                $size = getimagesizefromstring($value);

                if (! $size || $size[0] == 0 || $size[1] == 0 || ! $size['mime']) {
                    $fail($attribute.'is invalid');
                }
            },
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        Log::info('Starting student registration process');

        // create the user
        $user = User::create([
            'firstname'                            => $data['firstname'],
            'lastname'                             => $data['lastname'],
            backpack_authentication_column()       => $data[backpack_authentication_column()],
            'password'                             => bcrypt($data['password']),
        ]);

        Log::info('New user created with ID '.$user->id);

        // create the student

        $student = Student::create([
            'user_id' => $user->id,
            'idnumber'                             => $data['idnumber'],
            'birthdate'                            => Carbon::parse($data['birthdate'])->toDateTimeString(),
            'address'                              => $data['address'],
        ]);

        Log::info('New student created with ID '.$student->id);

        return $student;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
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
        $profession = Profession::firstOrCreate([
            'name' => $request->data['profession'],
        ]);

        $student->update([
            'profession_id' => $profession->id,
        ]);

        $institution = Institution::firstOrCreate([
            'name' => $request->data['institution'],
        ]);

        $student->update([
            'institution_id' => $institution->id,
        ]);

        Log::info('Profession and institution added to the student profile');

        // add photo

        $student
           ->addMediaFromBase64($request->data['userPicture'])
           ->usingFileName('profilePicture.jpg')
           ->toMediaCollection('profile-picture');

        Log::info('Profile picture added to the student profile');

        // add contact(s)
        foreach ($request->data['contacts'] as $contact) {
            $newContact = Contact::create([
                'student_id' => $student->id,
                'firstname' => $contact['firstname'],
                'lastname' => $contact['lastname'],
                'idnumber' => $contact['idnumber'],
                'address' => $contact['address'],
                'email' => $contact['email'],
                'invoiceable' => $contact['invoiceable'],
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
