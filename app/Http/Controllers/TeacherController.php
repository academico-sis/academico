<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Models\Teacher;

class TeacherController extends Controller
{
    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return redirect()->back();
    }

    public function restore($id): RedirectResponse
    {
        Teacher::withTrashed()
        ->whereId($id)
        ->restore();

        return redirect()->back();
    }
}
