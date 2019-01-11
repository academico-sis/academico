<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Validator;
use \App\Models\Rule;
use App\Models\PhoneNumber;
use App\Models\User;

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
            'idnumber'                             => 'required|max:255|unique:user_data',
            'genre_id'                             => 'required',
            'birthdate'                            => 'required|date',
            'phone_number'                         => 'required',
            'address'                              => 'required',
            backpack_authentication_column()       => 'required|'.$email_validation.'max:255|unique:'.$users_table,
            'password'                             => 'required|min:6|confirmed',
            'rules'                                => 'required'
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

        return $user->create([
            'firstname'                            => $data['firstname'],
            'lastname'                             => $data['lastname'],
            'idnumber'                             => $data['idnumber'],
            'genre_id'                             => $data['genre_id'],
            'birthdate'                            => $data['birthdate'],
            'phone_number'                         => $data['phone_number'],
            'address'                              => $data['address'],
            backpack_authentication_column()       => $data[backpack_authentication_column()],
            'password'                             => bcrypt($data['password'])
        ]);

                
    }
    
    public function register_rules_acceptation($user)
    {
        $rule = new Rule;
        $rule->user_id = $user;
        $rule->save();
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
        $phone->phoneable_type = User::class;
        $phone->phone_number = $request->input('phone_number');
        $phone->save();

        // create a new record that the user has accepted the rules.
        $this->register_rules_acceptation($user->id);

        // assign the role STUDENT to the new user
        $user->assignRole('student');

        // flash a confirmation message
        \Alert::success('The user has successfully been registered')->flash();

        // if invoice data has been required; redirect to the contact add form
        if($request->input('invoice_data')) {
            $user_id = $user->id;
            \Alert::success('The additional info has successfully been saved')->flash();
            return view('backpack::auth.invoice_data', compact('user_id'));
        }

        // redirect to the home page (login)
        return redirect('/home');
    }

}
