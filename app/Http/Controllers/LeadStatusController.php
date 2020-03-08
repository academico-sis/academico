<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadStatusController extends Controller
{
    private function subscribeToList($subscriber, $list) {
        $api_key = Config::where('name', 'mailerlite_api_key')->first()->value;
        $groupsApi = (new \MailerLiteApi\MailerLite($api_key))->groups();

        $subscribersApi = (new \MailerLiteApi\MailerLite($api_key))->subscribers();

        $subscriberGroups = $subscribersApi->getGroups($subscriber['email']); // returns array of group objects subscriber belongs to
        foreach ($subscriberGroups as $group)
        {
            $groupsApi->removeSubscriber($group->id, $subscriber['email']); // returns empty response
        }

        $addedSubscriber = $groupsApi->addSubscriber($list, $subscriber); // returns added subscriber
    }

    public function update(Request $request)
    {
        // create or update the lead status record for the selected student
        $student = Student::findOrFail($request->input('student'));
        $student->lead_type_id = $request->input('status');
        $student->save();

        // converted, active clients
        if ($request->input('status') == 1)
        {
            $groupId = Config::where('name', 'mailerlite_students_group_id')->first()->value;
            $parents_groupId = Config::where('name', 'mailerlite_parents_group_id')->first()->value;

            $subscriber = [
                'email' => $student->email,
                'name' => $student->firstname,
                'fields' => [
                  'lastname' => $student->lastname
                ]
            ];
            $this->subscribeToList($subscriber, $groupId);
        }

            
        // oldstudent
        if ($request->input('status') == 2 || $request->input('status') == 3 || $request->input('status') == 4)
        {
            $groupId = Config::where('name', 'mailerlite_oldstudents_group_id')->first()->value;
            $parents_groupId = Config::where('name', 'mailerlite_oldstudents_group_id')->first()->value;

            $subscriber = [
                'email' => $student->email,
                'name' => $student->firstname,
                'fields' => [
                  'lastname' => $student->lastname
                ]
            ];
            $this->subscribeToList($subscriber, $groupId);
        }


        // contacts
        foreach($student->contacts as $contact)
        {
            $subscriber = [
                'email' => $contact->email,
                'name' => $contact->firstname,
                'fields' => [
                  'lastname' => $contact->lastname
                ]
            ];
            $this->subscribeToList($subscriber, $parents_groupId);
        }


        return $student->lead_type_id;
    }

    public function reset_all_converted_leads()
    {
        $api_key = Config::where('name', 'mailerlite_api_key')->first()->value;

        $students = Student::where('lead_type_id', '=', 1)->get();
        $groupId = Config::where('name', 'mailerlite_oldstudents_group_id')->first()->value;
        $parents_groupId = Config::where('name', 'mailerlite_oldstudents_group_id')->first()->value;
        $groupsApi = (new \MailerLiteApi\MailerLite($api_key))->groups();

        $previousGroupId = Config::where('name', 'mailerlite_students_group_id')->first()->value;
        $previousParentsGroupId = Config::where('name', 'mailerlite_parents_group_id')->first()->value;

        $students = [];
        $parents = [];

        foreach ($students as $student) {
            $student->update(['lead_type_id' => 4]);

            // unsubscribe from current students group
            $groupsApi->removeSubscriber($previousGroupId, $student->email); // returns empty response

            array_push($students, [
                'email' => $student->email,
                'name' => ucwords($student->firstname),
                'fields' => [
                    'last_name' => ucwords($student->lastname),
                    'birthdate' => $student->birthdate ?? ''
            ]]);

            foreach ($student->contacts as $contact)
            {
                $groupsApi->removeSubscriber($previousParentsGroupId, $contact->email); // returns empty response

                array_push($parents,
                [
                    'email' => $contact->email,
                    'name' => ucwords($contact->firstname),
                    'fields' => [
                        'last_name' => ucwords($contact->lastname),
                ]]);
            }


            $options = [
                'resubscribe' => false,
                'autoresponders' => false // send autoresponders for successfully imported subscribers 
            ];
        }

        $groupsApi->importSubscribers($groupId, $students, $options);
        $groupsApi->importSubscribers($parents_groupId, $parents, $options);

        return back();
    }
}
