<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

 /**
     * Display the specified resource.
     */
    public function show(User $teacher)
    {
        $events = $teacher->events->toArray();
        $events = array_map(function($event) {
            return array(
                'title' => $event['name'],
                'start' => $event['start'],
                'end' => $event['end']
            );
        }, $events);

        return view('calendars.simple', [
            'events' => $events,
            'ressource' => $teacher,
        ]);
    }

}
