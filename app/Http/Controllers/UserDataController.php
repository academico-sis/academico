<?php

namespace App\Http\Controllers;

use App\Models\UserData;
use App\Models\PhoneNumber;

use Illuminate\Http\Request;

class UserDataController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        $phone = new PhoneNumber;
        $phone->phoneable_id = $contact->id;
        $phone->phoneable_type = UserData::class;
        $phone->phone_number = $request->input('phone_number');
        $phone->save();

        \Alert::success('The information has successfully been saved')->flash();

        return redirect('/home');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserData  $userData
     * @return \Illuminate\Http\Response
     */
    public function show(UserData $userData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserData  $userData
     * @return \Illuminate\Http\Response
     */
    public function edit(UserData $userData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserData  $userData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserData $userData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserData  $userData
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserData $userData)
    {
        //
    }
}
