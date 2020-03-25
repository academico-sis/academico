<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Student;
use Illuminate\Http\Request;

class LeadStatusController extends Controller
{
    private function subscribeToList($subscriber, $list)
    {
        $api_key = Config::where('name', 'mailerlite_api_key')->first()->value;
        $groupsApi = (new \MailerLiteApi\MailerLite($api_key))->groups();

        $subscribersApi = (new \MailerLiteApi\MailerLite($api_key))->subscribers();

        $subscriberGroups = $subscribersApi->getGroups($subscriber['email']); // returns array of group objects subscriber belongs to
        foreach ($subscriberGroups as $group) {
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

        // if the sync with external mailing system is enabled
        if (config('settings.external_mailing_enabled') == true) {
            $activeStudentsGroupId = Config::where('name', 'mailerlite_students_group_id')->first()->value;
            $inactiveStudentsGroupId = Config::where('name', 'mailerlite_oldstudents_group_id')->first()->value;

            // converted, active clients
            if ($request->input('status') == 1) {
                $subscriber = [
                    'email' => $student->email,
                    'name' => $student->firstname,
                    'fields' => [
                        'lastname' => $student->lastname,
                    ],
                ];
                $this->subscribeToList($subscriber, $activeStudentsGroupId);

                // contacts
                foreach ($student->contacts as $contact) {
                    $subscriber = [
                        'email' => $contact->email,
                        'name' => $contact->firstname,
                        'fields' => [
                            'lastname' => $contact->lastname,
                        ],
                    ];
                    $this->subscribeToList($subscriber, $activeStudentsGroupId);
                }
            }

            // inactive or formerClient
            if ($request->input('status') == 2 || $request->input('status') == 3) {
                $subscriber = [
                    'email' => $student->email,
                    'name' => $student->firstname,
                    'fields' => [
                        'lastname' => $student->lastname,
                    ],
                ];
                $this->subscribeToList($subscriber, $inactiveStudentsGroupId);

                // contacts
                foreach ($student->contacts as $contact) {
                    $subscriber = [
                        'email' => $contact->email,
                        'name' => $contact->firstname,
                        'fields' => [
                            'lastname' => $contact->lastname,
                        ],
                    ];
                    $this->subscribeToList($subscriber, $inactiveStudentsGroupId);
                }
            }
        }

        return $student->lead_type_id;
    }

    public function reset_all_converted_leads()
    {
        // change all active students to potential
        Student::where('lead_type_id', '=', 1)->update(['lead_type_id' => 4]);
    }
}
