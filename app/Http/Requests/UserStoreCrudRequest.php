<?php

namespace App\Http\Requests;

class UserStoreCrudRequest extends \Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest
{
    public function rules()
    {
        return [
            'email'         => 'required|unique:'.config('permission.table_names.users', 'users').',email',
            'firstname'     => 'required',
            'lastname'      => 'required',
            'password'      => 'required',
        ];
    }
}
