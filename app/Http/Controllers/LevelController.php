<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\RedirectResponse;

class LevelController extends Controller
{
    public function destroy(Level $level): RedirectResponse
    {
        $level->delete();

        return redirect()->back();
    }

    public function restore($id): RedirectResponse
    {
        Level::withTrashed()
        ->whereId($id)
        ->restore();

        return redirect()->back();
    }
}
