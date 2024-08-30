<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;

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
