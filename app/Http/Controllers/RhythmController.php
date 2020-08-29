<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rhythm;

class RhythmController extends Controller
{
    public function destroy(Rhythm $rhythm)
    {
        $rhythm->delete();
        
        return redirect()->back();
    }

    public function restore($id)
    {
        Rhythm::withTrashed()
        ->whereId($id)
        ->restore();
        
        return redirect()->back();
    }
}
