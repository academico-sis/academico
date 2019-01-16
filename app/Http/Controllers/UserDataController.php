<?php

namespace App\Http\Controllers;

use App\Models\UserData;
use App\Models\PhoneNumber;

use Illuminate\Http\Request;
use App\Http\Requests\UserDataRequest as StoreRequest;

class UserDataController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $contact = new UserData;
        $contact->user_id = $request->input('user_id');
        $contact->firstname = $request->input('firstname');
        $contact->lastname = $request->input('lastname');
        $contact->idnumber = $request->input('idnumber');
        $contact->address = $request->input('address');
        $contact->email = $request->input('email');
        //$contact->relationship_id = $request->input('relationship_id');
        $contact->save();

        // register the phone number
        if(($request->input('phone_number')) !== null)
        {
            $phone = new PhoneNumber;
            $phone->phoneable_id = $contact->id;
            $phone->phoneable_type = UserData::class;
            $phone->phone_number = $request->input('phone_number');
            $phone->save();
        }

        \Alert::success('The information has successfully been saved')->flash();

    }
}
