<?php
namespace App\Http\Controllers\Auth;

namespace App\Http\Controllers\Auth;

use Validator;
use Carbon\Carbon;
use App\Models\User;
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
            'idnumber'                             => 'required|max:255|unique:contacts',
            'genre_id'                             => 'required|numeric',
            'birthdate'                            => 'required|date',
            'phone_number'                         => 'required',
            'address'                              => 'required',
            backpack_authentication_column()       => 'required|'.$email_validation.'max:255|unique:'.$users_table,
            'password'                             => 'required|min:6|confirmed',
            'rules'                                => 'required|boolean'
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
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();

        $user = User::create([
            'firstname'                            => $data['firstname'],
            'lastname'                             => $data['lastname'],
            backpack_authentication_column()       => $data[backpack_authentication_column()],
            'password'                             => bcrypt($data['password'])
        ]);

        return Student::create([
            'user_id'                              => $user->id,
            'idnumber'                             => $data['idnumber'],
            'genre_id'                             => $data['genre_id'],
            'birthdate'                            => $data['birthdate'],
            'address'                              => $data['address'],
        ]);

    }
    
    public function register_rules_acceptation(Student $student)
    {
        $student->terms_accepted_at = Carbon::now()->toDateTimeString();
        $student->save();
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

        $this->validator($request->all())->validate();
        $user = $this->create($request->all());

        // register the phone number
        $phone = new PhoneNumber;
        $phone->phoneable_id = $user->id;
        $phone->phoneable_type = Student::class;
        $phone->phone_number = $request->input('phone_number');
        $phone->save();

        // create a new record that the user has accepted the rules.
        $this->register_rules_acceptation($user);

        // flash a confirmation message
        \Alert::success(__('The user has successfully been registered'))->flash();
        Log::info('New user registered with ID ' . $user->id);

        // if invoice data has been required; log the user in and open the form to add a contact
        if($request->input('invoice_data')) {
            backpack_auth()->login(User::find($user->user_id), false); // do not remember the user
            $student_id = $user->id;
            return view('backpack::auth.invoice_data', compact('student_id'));
        }

        // redirect to the home page (login)
        return redirect()->route('home');
    }

}
