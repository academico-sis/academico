<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use App\Models\Institution;
use Illuminate\Http\Request;

class DataUpdateController extends Controller
{
    public function index()
    {
        $user = backpack_user();

        return view('student/update', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $result = backpack_user()->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
        ]);

        \Alert::success(__('Your data has been saved'))->flash();
        return redirect('/update/2');
    }

    public function index2()
    {
        $user = backpack_user();

        return view('student/update2', [
            'user' => $user,
        ]);
    }

    public function update2(Request $request)
    {
        $result = backpack_user()->student()->update([
            'address' => $request->address,
            'idnumber' => $request->idnumber,
            'birthdate' => $request->birthdate,
        ]);

        \Alert::success(__('Your data has been saved'))->flash();
        return redirect('/update/3');
    }

    public function index3()
    {
        $user = backpack_user();

        return view('student/update3', [
            'user' => $user,
        ]);
    }

    public function index4()
    {
        $user = backpack_user();

        return view('student/update4', [
            'user' => $user,
        ]);
    }

    public function update4(Request $request)
    {
        $profession = Profession::create([
            'name' => $request->profession,
        ]);

        backpack_user()->student()->update([
            'profession_id' => $profession->id
        ]);


        $institution = Institution::create([
            'name' => $request->institution,
        ]);

        backpack_user()->student()->update([
            'institution_id' => $institution->id
        ]);

        \Alert::success(__('Your data has been saved'))->flash();
        return redirect('/update/5');
    }

    public function index5()
    {
        $user = backpack_user();

        return view('student/update5', [
            'user' => $user,
        ]);
    }

    public function index6()
    {
        $user = backpack_user();

        return view('student/update6', [
            'user' => $user,
        ]);
    }
}
