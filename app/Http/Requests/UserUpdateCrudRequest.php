<?php

namespace App\Http\Requests;

class UserUpdateCrudRequest extends \Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|unique:'.config('permission.table_names.users', 'users').',email,'.$this->get('id'),
            'firstname'     => 'required',
            'lastname' => 'required',
        ];
    }
}
