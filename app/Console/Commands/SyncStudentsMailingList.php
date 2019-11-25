<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\Period;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Console\Command;

/** wip - todo - the interface we use */
interface Mailer {
    /**
     * createSubscriber
     *
     * @param $student object with email, firstname, lastname
     * @param int $group
     * @return void
     */
    public function createSubscriber($student, $group);
    
    /**
     * unsubscribeSubscriber
     *
     * @param string $email
     * @return void
     */
    public function unsubscribeSubscriber($email);
}

class SyncStudentsMailingList extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncmailing:students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync users with an external mailing service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->api_key = Config::where('name', 'mailerlite_api_key')->first()->value;
    }


    private function subscribeUsersToList($users, $list_id)
    {
        $groupsApi = (new \MailerLiteApi\MailerLite($this->api_key))->groups();

        $options = [
            'resubscribe' => true,
            'autoresponders' => false // send autoresponders for successfully imported subscribers 
        ];

        $groupsApi->importSubscribers($list_id, $users, $options); // returns added subscriber
    }

    private function removeSubscriberFromAllGroups($email)
    {
        $subscribersApi = (new \MailerLiteApi\MailerLite($this->api_key))->subscribers();
        $subscriberGroups = $subscribersApi->getGroups($email); // returns array of group objects subscriber belongs to
        $groupsApi = (new \MailerLiteApi\MailerLite($this->api_key))->groups();
        
        foreach ($subscriberGroups as $group)
        {
            $groupsApi->removeSubscriber($group->id, $email); // returns empty response
            dump('removed from group');
        }
    }


    private function unsubscribeSubscriber($email)
    {

        $subscribersApi = (new \MailerLiteApi\MailerLite($this->api_key))->subscribers();

        // change type of subscriber
        $subscriberData = [
            'type' => 'unsubscribed',
        ];

        return $subscribersApi->update($email, $subscriberData); // returns object of updated subscriber


    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $period = Period::get_default_period();

        $enrollments = Enrollment::all();

        $groupsApi = (new \MailerLiteApi\MailerLite($this->api_key))->groups();


        // create subscribers from all students and associated contact in period

        $students = [];
        $parents = [];
        $oldStudents = [];
        $alumni = [];
        
        foreach ($enrollments as $enrollment)
        {
            if($enrollment->student->lead_type_id == 3) // dead
            {
                // unsubscribe
                $this->unsubscribeSubscriber($enrollment->student->email);

                foreach ($enrollment->student->contacts as $contact)
                {
                    dump($this->unsubscribeSubscriber($contact->email));
                }
            }


            if ($enrollment->student->lead_type_id == 1) // converted
            {
                $this->removeSubscriberFromAllGroups($enrollment->student->email);

                array_push($students, [
                    'email' => $enrollment->student->email,
                    'name' => ucwords($enrollment->student->firstname),
                    'fields' => [
                        'last_name' => ucwords($enrollment->student->lastname),
                        'birthdate' => $enrollment->student->birthdate ?? ''
                ]]);

                foreach ($enrollment->student->contacts as $contact)
                {
                    $this->removeSubscriberFromAllGroups($contact->email);

                    array_push($parents,
                    [
                        'email' => $contact->email,
                        'name' => ucwords($contact->firstname),
                        'fields' => [
                            'last_name' => ucwords($contact->lastname),
                    ]]);
                }
            }


            if($enrollment->student->lead_type_id == 6) // exAlumno
            {
                $this->removeSubscriberFromAllGroups($enrollment->student->email);

                array_push($alumni, [
                    'email' => $enrollment->student->email,
                    'name' => ucwords($enrollment->student->firstname),
                    'fields' => [
                        'last_name' => ucwords($enrollment->student->lastname),
                        'birthdate' => $enrollment->student->birthdate ?? ''
                    ]
                ]);

                foreach ($enrollment->student->contacts as $contact)
                {
                    $this->removeSubscriberFromAllGroups($contact->email);

                    array_push($alumni, [
                        'email' => $contact->email,
                        'name' => ucwords($contact->firstname),
                        'fields' => [
                            'last_name' => ucwords($contact->lastname),
                        ]
                    ]);
                }
            }

            if($enrollment->student->lead_type_id == 7) // oldStudent
            {
                $this->removeSubscriberFromAllGroups($enrollment->student->email);

                array_push($oldStudents, [
                    'email' => $enrollment->student->email,
                    'name' => ucwords($enrollment->student->firstname),
                    'fields' => [
                        'last_name' => ucwords($enrollment->student->lastname),
                        'birthdate' => $enrollment->student->birthdate ?? ''
                    ]
                ]);

                foreach ($enrollment->student->contacts as $contact)
                {
                    $this->removeSubscriberFromAllGroups($contact->email);

                    array_push($oldStudents, [
                        'email' => $contact->email,
                        'name' => ucwords($contact->firstname),
                        'fields' => [
                            'last_name' => ucwords($contact->lastname),
                        ]
                    ]);
                }
            }

        }


        dump($this->subscribeUsersToList($students, 8868042));
        dump($this->subscribeUsersToList($oldStudents, 10862804));
        dump($this->subscribeUsersToList($alumni, 10862806));
        dump($this->subscribeUsersToList($parents, 10862650));

    }
}
