<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Profession;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /* Return search results for enrollment modal */
    public function search(Request $request)
    {
        // If the user is not allowed to perform this action
        if (Gate::forUser(backpack_user())->denies('enroll-students')) {
            abort(403);
        }

        $data = [];

        if ($request->has('q')) {
            $search = $request->q;

            $data = DB::table('students')
                    ->select('students.id', 'users.firstname', 'users.lastname')
                    ->join('users', 'students.user_id', '=', 'users.id')
                    ->where('users.firstname', 'LIKE', "%$search%")
                    ->orWhere('users.lastname', 'LIKE', "%$search%")
                    ->get();
        }

        return response()->json($data);
    }

    public function create()
    {
        $student = (new Student);

        return view('students.edit', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstname'                            => 'required|max:255',
            'lastname'                             => 'required|max:255',
            'email'                                => 'required|unique:users',
        ]);

        // update the user info
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make(Str::random(12)),
        ]);

        // update the student info

        $student = Student::create([
            'user_id' => $user->id,
            'idnumber' => $request->idnumber,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'birthdate' => $request->birthdate,
        ]);

        // save profession and institution
        if ($request->profession) {
            $profession = Profession::firstOrCreate([
                'name' => $request->profession,
            ]);

            $student->update([
                'profession_id' => $profession->id,
            ]);
        }

        if ($request->institution) {
            $institution = Institution::firstOrCreate([
                'name' => $request->institution,
            ]);

            $student->update([
                'institution_id' => $institution->id,
            ]);
        }

        return redirect()->route('student.index');
    }

    public function update(Student $student, Request $request)
    {
        $request->validate([
            'firstname'                            => 'required|max:255',
            'lastname'                             => 'required|max:255',
            'email' => [
                'required',
                Rule::unique('users')->ignore($student->user),
            ],
        ]);

        // update the user info
        $student->user()->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
        ]);

        // update the student info

        $student->update([
            'idnumber' => $request->idnumber,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'birthdate' => $request->birthdate,
        ]);

        // save profession and institution
        if ($request->profession) {
            $profession = Profession::firstOrCreate([
                'name' => $request->profession,
            ]);

            $student->update([
                'profession_id' => $profession->id,
            ]);
        }

        if ($request->institution) {
            $institution = Institution::firstOrCreate([
                'name' => $request->institution,
            ]);

            $student->update([
                'institution_id' => $institution->id,
            ]);
        }
        
        return redirect()->route('student.show', ['id' => $student->id]);
    }
}
