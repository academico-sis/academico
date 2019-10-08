<?php
namespace App\Http\Controllers\Auth;

namespace App\Http\Controllers\Auth;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Contact;
use App\Models\Student;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends \Backpack\Base\app\Http\Controllers\Auth\RegisterController
{



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
            'cedula_check'                         => 'required|1',
            'idnumber'                             => 'required',
            'address'                              => 'required',
            'phonenumber'                          => 'required',
            'tc_consent'                           => 'required',
            'address'                              => 'required',
            'birthdate'                            => 'required',
            'profession'                           => 'required',
            'institution'                          => 'required'
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
            'password'                             => bcrypt($data['password'])
        ]);

        Log::info('New user created with ID ' . $user->id);

        // create the student
        
        $student = Student::create([
            'user_id' => $user->id,
            'idnumber'                             => $data['idnumber'],
            'birthdate'                            => Carbon::parse($data['birthdate'])->toDateTimeString(),
            'address'                              => $data['address'],
        ]);

        Log::info('New student created with ID ' . $student->id);
        
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
        if (!config('backpack.base.registration_open')) {
            abort(403, trans('backpack::base.registration_closed'));
        }
        
        $this->validator($request->data);
        $student = $this->create($request->data);
        
        // add phone number(s)

        foreach ($request->data['phonenumbers'] as $number) {
            PhoneNumber::create([
                'phoneable_id' => $student->id,
                'phoneable_type' => Student::class,
                'phone_number' => $number['number']
            ]);
        }

        Log::info('Phone numbers added to the student profile');


        // add profession and institution

        Log::info('Profession and institution added to the student profile');

        // add photo

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
            ]);

            foreach ($contact['phonenumbers'] as $number) {
                PhoneNumber::create([
                    'phoneable_id' => $newContact->id,
                    'phoneable_type' => Contact::class,
                    'phone_number' => $number['number']
                ]);
            }
        }
        
        Log::info('Aditional contacts associated to the student profile');

        // flash a confirmation message
        \Alert::success(__('The user has successfully been registered'))->flash();
        
    }

}
