<?php

namespace App\Http\Controllers\Auth;

use Alert;
use Auth;
use Backpack\Base\app\Http\Controllers\Controller;
use Backpack\Base\app\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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
        $this->data['user'] = $this->guard()->user()->student;

        return view('backpack::auth.account.update_student_info', $this->data);
    }

    /**
     * Save the modified personal information for a user.
     */
    public function postStudentInfoForm(Request $request)
    {
        $result = $this->guard()->user()->student()->update($request->except(['_token']));

        if ($result) {
            Alert::success(trans('backpack::base.account_updated'))->flash();
        } else {
            Alert::error(trans('backpack::base.error_saving'))->flash();
        }

        return redirect()->back();
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
