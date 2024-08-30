<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Models\Level;

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
