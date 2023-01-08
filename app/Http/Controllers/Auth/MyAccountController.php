<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ContactRelationship;
use App\Models\Institution;
use App\Models\Profession;
use App\Models\Student;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prologue\Alerts\Facades\Alert;

class MyAccountController extends Controller
{
    protected array $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the user a form to change his personal information.
     */
    public function getAccountInfoForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('student.account.update_info', $this->data);
    }

    /**
     * Save the modified personal information for a user.
     */
    public function postAccountInfoForm(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
        ]);

        $result = $this->guard()->user()->update($request->except(['_token', 'username']));
        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();

            // if the user has been selected for a forced update, move to the next step
            if ($this->guard()->user()->isStudent() && $this->guard()->user()->student->force_update == 1) {
                $this->guard()->user()->student->update(['force_update' => 2]);
                return redirect('edit/2');
            }
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    public function getChangePasswordForm()
    {
        $this->data['title'] = trans('Change password');
        $this->data['user'] = $this->guard()->user();

        return view('student.account.change_password', $this->data);
    }

    public function postChangePasswordForm(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);

        $result = $this->guard()->user()->update([
            'password' => bcrypt($request->password),
        ]);

        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();

            // if the user has been selected for a forced update, move to the next step
            if ($this->guard()->user()->isStudent() && $this->guard()->user()->student->force_update == 2) {
                $this->guard()->user()->student->update(['force_update' => 3]);
                return redirect('edit/3');
            }
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Show the student a form to change his personal information.
     * The difference with getAccountInfoForm is that the former is available to all users. This one is specific to stuents (different DB tables).
     */
    public function getStudentInfoForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('student.account.update_student_info', $this->data);
    }

    /**
     * Save the modified personal information for a user.
     */
    public function postStudentInfoForm(Request $request)
    {
        $request->validate([
            'idnumber' => 'required',
            'address' => 'required',
            'birthdate' => 'required',
        ]);

        Student::updateOrCreate(
            ['id' => $this->guard()->user()->id],
            [
                'idnumber' => $request->idnumber,
                'address' => $request->address,
                'birthdate' => $request->birthdate,
            ]
        );

        Alert::success(trans('backpack::base.account_updated'))->flash();

        // if the user has been selected for a forced update, move to the next step
        if ($this->guard()->user()->student->force_update == 3) {
            $this->guard()->user()->student->update(['force_update' => 4]);
        }

        return redirect()->to('/');
    }

    /**
     * Show the phone numbers edit screen.
     */
    public function getPhoneForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('student.account.update_phone', $this->data);
    }

    /**
     * Move the update step after reviewing the phone numbers.
     */
    public function postPhoneForm()
    {
        // if the user has been selected for a forced update, move to the next step
        if ($this->guard()->user()->student->force_update == 4) {
            $this->guard()->user()->student->update(['force_update' => 5]);
        }

        Alert::success(__('Your data has been saved'))->flash();

        return redirect()->to('/');
    }

    /**
     * Show the phone numbers edit screen.
     */
    public function getAccountProfessionForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('student.account.update_profession', $this->data);
    }

    public function postAccountProfessionForm(Request $request)
    {
        $request->validate([
            'profession' => 'required',
            'institution' => 'required',
        ]);

        $profession = Profession::firstOrCreate([
            'name' => $request->profession,
        ]);

        $this->guard()->user()->student()->update([
            'profession_id' => $profession->id,
        ]);

        $institution = Institution::firstOrCreate([
            'name' => $request->institution,
        ]);

        $this->guard()->user()->student()->update([
            'institution_id' => $institution->id,
        ]);

        // if the user has been selected for a forced update, move to the next step
        if ($this->guard()->user()->student->force_update == 5) {
            $this->guard()->user()->student->update(['force_update' => 6]);
        }

        Alert::success(__('Your data has been saved'))->flash();
        Log::info('User updated their data step 4');

        return redirect()->to('/');
    }

    /**
     * Show the photo edit screen.
     */
    public function getPhotoForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('student.account.update_photo', $this->data);
    }

    public function postPhotoForm(Request $request)
    {
        if ($request->fileToUpload != null) {
            $user = Student::where('id', $this->guard()->user()->id)->first();

            $user
               ->addMedia($request->fileToUpload)
               ->toMediaCollection('profile-picture');
        }

        // if the user has been selected for a forced update, move to the next step
        if ($this->guard()->user()->student->force_update == 6) {
            $this->guard()->user()->student->update(['force_update' => 7]);
        }

        Alert::success(__('Your picture has been saved'))->flash();
        Log::info('User updated their data step 5');

        return redirect()->to('/');
    }

    /**
     * Show the additional contacts review screen.
     */
    public function getContactsForm()
    {
        $this->data['title'] = trans('backpack::base.my_account');
        $this->data['user'] = $this->guard()->user();
        $this->data['contact_types'] = ContactRelationship::all();

        return view('student.account.additional_contacts', $this->data);
    }

    public function postContactsForm(Request $request)
    {
        if ($this->guard()->user()->student->force_update == 7) {
            $this->guard()->user()->student->update(['force_update' => null]);
        }
        Log::info('User updated their data step 6');

        if ($request->session()->has('logout')) {
            backpack_auth()->logout();
            $request->session()->flush();
        }

        return redirect()->to('/');
    }

    /**
     * Get the guard to be used for account manipulation.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return backpack_auth();
    }
}
