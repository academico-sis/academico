<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Models\Rhythm;

class RhythmController extends Controller
{
    public function destroy(Rhythm $rhythm): RedirectResponse
    {
        $rhythm->delete();

        return redirect()->back();
    }

    public function restore($id): RedirectResponse
    {
        Rhythm::withTrashed()
        ->whereId($id)
        ->restore();

        return redirect()->back();
    }
}
