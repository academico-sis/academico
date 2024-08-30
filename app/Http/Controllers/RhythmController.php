<?php

namespace App\Http\Controllers;

use App\Models\Rhythm;
use Illuminate\Http\RedirectResponse;

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
