<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Level;

class LevelController extends Controller
{
    public function destroy(Level $level)
    {
        $level->delete();
        
        return redirect()->back();
    }

    public function restore($id)
    {
        Level::withTrashed()
        ->whereId($id)
        ->restore();
        
        return redirect()->back();
    }
}
