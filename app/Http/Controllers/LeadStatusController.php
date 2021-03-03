<?php

namespace App\Http\Controllers;

use App\Events\LeadStatusUpdatedEvent;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Config;

class LeadStatusController extends Controller
{
    public function update(Request $request)
    {
        // create or update the lead status record for the selected student
        $student = Student::findOrFail($request->input('student'));
        $student->lead_type_id = $request->input('status');
        $student->save();

        // if the sync with external mailing system is enabled
        if (config('mailing-system.external_mailing_enabled') == true) {

            match ($request->input('status')) {
                1 => $listId = config('mailing-system.mailerlite.activeStudentsListId'),
                2, 3 => $listId = config('mailing-system.mailerlite.inactiveStudentsListId'),
                default => abort(422, 'List ID not found'),
            };

            LeadStatusUpdatedEvent::dispatch($student, $listId);

            foreach ($student->contacts as $contact) {
                LeadStatusUpdatedEvent::dispatch($contact, $listId);
            }
        }

        return $student->lead_type_id;
    }
}
