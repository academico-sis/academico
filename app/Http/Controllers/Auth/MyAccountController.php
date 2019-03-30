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
        $this->guard()->user()->student()->update([
            'idnumber' => $request->idnumber,
            'address' => $request->address,
            'birthdate' => $request->birthdate,
            ]);


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
