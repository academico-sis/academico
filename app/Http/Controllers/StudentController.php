<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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
}
