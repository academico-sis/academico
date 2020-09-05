<?php

namespace App\Http\Controllers;

use App\Models\Teacher;

class TeacherController extends Controller
{
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->back();
    }

    public function restore($id)
    {
        Teacher::withTrashed()
        ->whereId($id)
        ->restore();

        return redirect()->back();
    }
}
