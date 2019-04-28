<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Profession;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $user = backpack_user();


        $result = backpack_user()->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
        ]);
        
        $user->student()->update([
            'force_update' => 2
        ]);
        
        \Alert::success(__('Your data has been saved'))->flash();
        Log::info('User updated their data step 1');

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
        $user = backpack_user();

        $result = backpack_user()->student()->update([
            'address' => $request->address,
            'idnumber' => $request->idnumber,
            'birthdate' => $request->birthdate,
        ]);

        $user->student()->update([
            'force_update' => 3
        ]);

        \Alert::success(__('Your data has been saved'))->flash();
        Log::info('User updated their data step 2');

        return redirect('/update/3');
    }

    public function index3()
    {
        $user = backpack_user();

        return view('student/update3', [
            'user' => $user,
        ]);
    }

    public function update3()
    {
        $user = backpack_user();
        $user->student()->update([
            'force_update' => 4
        ]);

        \Alert::success(__('Your data has been saved'))->flash();
        Log::info('User updated their data step 3');

        return redirect('/update/4');
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
        $profession = Profession::updateOrCreate([
            'name' => $request->profession,
        ]);

        backpack_user()->student()->update([
            'profession_id' => $profession->id
        ]);


        $institution = Institution::updateOrCreate([
            'name' => $request->institution,
        ]);

        backpack_user()->student()->update([
            'institution_id' => $institution->id
        ]);

        $user = backpack_user();
        $user->student()->update([
            'force_update' => 5
        ]);

        \Alert::success(__('Your data has been saved'))->flash();
        Log::info('User updated their data step 4');

        return redirect('/update/5');
    }

    public function index5()
    {
        $user = backpack_user();

        return view('student/update5', [
            'user' => $user,
        ]);
    }

    public function update5(Request $request)
    {
        if ($request->fileToUpload != null)
        {
            $user = Student::where('user_id', backpack_user()->id)->first();
        
            $user
               ->addMedia($request->fileToUpload)
               ->toMediaCollection();
        }
        
           $user = backpack_user();
           $user->student()->update([
               'force_update' => 6
           ]);

        \Alert::success(__('Your picture has been saved'))->flash();
        Log::info('User updated their data step 5');

        return redirect('/update/6');
    }

    public function index6()
    {
        $user = backpack_user();

        return view('student/update6', [
            'user' => $user,
        ]);
    }

    public function finishUpdate()
    {
        $user = backpack_user();

        $user->student()->update([
            'force_update' => 0
        ]);
        Log::info('User updated their data step 6');

        return redirect('/');
    }
}
