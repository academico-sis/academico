<?php

namespace App\Http\Controllers\Auth;

use Alert;
use App\Models\Student;
use App\Models\Profession;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Backpack\Base\app\Http\Controllers\Controller;
use Backpack\Base\app\Http\Requests\ChangePasswordRequest;

class MyAccountController extends Controller
{
    protected $data = [];

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the user a form to change his personal information.
     */
    public function getAccountInfoForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('backpack::auth.account.update_info', $this->data);
    }


    /**
     * Save the modified personal information for a user.
     */
    public function postAccountInfoForm(Request $request)
    {
        $result = $this->guard()->user()->update($request->except(['_token']));
        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();

            // if the user has been selected for a forced update, move to the next step
            if($this->guard()->user()->student->force_update == 1) {
                $this->guard()->user()->student->update(['force_update' => 2]);
            }
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }


    /**
     * Show the student a form to change his personal information.
     * The difference with getAccountInfoForm is that the former is available to all users. This one is specific to stuents (different DB tables)
     */
    public function getStudentInfoForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('backpack::auth.account.update_student_info', $this->data);
    }

    /**
     * Save the modified personal information for a user.
     */
    public function postStudentInfoForm(Request $request)
    {
        $student = Student::updateOrCreate(
            ['user_id' => $this->guard()->user()->id],
            [
                'idnumber' => $request->idnumber,
                'address' => $request->address,
                'birthdate' => $request->birthdate,
            ]
        );
        
        Alert::success(trans('backpack::base.account_updated'))->flash();

        // if the user has been selected for a forced update, move to the next step
        if($this->guard()->user()->student->force_update == 2 || $this->guard()->user()->force_update == null) {
            $this->guard()->user()->student->update(['force_update' => 3]);
        }

        return redirect('/');
    }


    /**
     * Show the phone numbers edit screen
     */
    public function getPhoneForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('backpack::auth.account.update_phone', $this->data);
    }

    /**
     * Move the update step after reviewing the phone numbers
     */
    public function postPhoneForm()
    {
        // if the user has been selected for a forced update, move to the next step
        if($this->guard()->user()->student->force_update == 3) {
            $this->guard()->user()->student->update(['force_update' => 4]);
        }

        \Alert::success(__('Your data has been saved'))->flash();

        return redirect('/');
    }

    /**
     * Show the phone numbers edit screen
     */
    public function getAccountProfessionForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('backpack::auth.account.update_profession', $this->data);
    }

    public function postAccountProfessionForm(Request $request)
    {

        $profession = Profession::firstOrCreate([
            'name' => $request->profession,
        ]);

        $this->guard()->user()->student()->update([
            'profession_id' => $profession->id
        ]);


        $institution = Institution::firstOrCreate([
            'name' => $request->institution,
        ]);

        $this->guard()->user()->student()->update([
            'institution_id' => $institution->id
        ]);

        // if the user has been selected for a forced update, move to the next step
        if($this->guard()->user()->student->force_update == 4) {
            $this->guard()->user()->student->update(['force_update' => 5]);
        }

        \Alert::success(__('Your data has been saved'))->flash();
        Log::info('User updated their data step 4');

        return redirect('/');
    }

    /**
     * Show the phone numbers edit screen
     */
    public function getPhotoForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('backpack::auth.account.update_photo', $this->data);
    }

    public function postPhotoForm(Request $request)
    {
        if ($request->fileToUpload != null)
        {
            $user = Student::where('user_id', $this->guard()->user()->id)->first();
        
            $user
               ->addMedia($request->fileToUpload)
               ->toMediaCollection();
        }
        
        // if the user has been selected for a forced update, move to the next step
        if($this->guard()->user()->student->force_update == 5) {
            $this->guard()->user()->student->update(['force_update' => 6]);
        }

        \Alert::success(__('Your picture has been saved'))->flash();
        Log::info('User updated their data step 5');

        return redirect('/');
    }

    /**
     * Show the additional contacts review screen
     */
    public function getContactsForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('backpack::auth.account.additional_contacts', $this->data);
    }

    public function postContactsForm()
    {
        if($this->guard()->user()->student->force_update == 6) {
            $this->guard()->user()->student->update(['force_update' => null]);
        }
        Log::info('User updated their data step 6');

        if (session()->has('logout')) {
            backpack_auth()->logout();
            session()->flush();
        }
        return redirect('/');
    }


    /**
     * Get the guard to be used for account manipulation.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }
}
