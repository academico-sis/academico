<?php

namespace App\Console\Commands;

use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Console\Command;

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
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->api_key = env('mailerlite_api_key');
    }

    private function add_users_to_group($source_users, $target_group_id)
    {
        $groupsApi = (new \MailerLiteApi\MailerLite($this->api_key))->groups();
        $subscribersApi = (new \MailerLiteApi\MailerLite($this->api_key))->subscribers();

        $target_users = $groupsApi->limit(10000)->getSubscribers($target_group_id);

        $all_subscribers = $subscribersApi->limit(10000)->get(); // todo watch this limit

        // step 1 - add source users to the target group
        foreach ($source_users as $student)
        {
            $match = false;
            $exist = false;

            unset($created_subscriber);

            foreach ($target_users as $subscriber)
            {
                if (strtolower($student->email) == strtolower($subscriber->email))
                {
                    $match = true;
                    break 1;
                }
            }
            if ($match == false) {
                // check if the student exists in ML
                foreach ($all_subscribers as $subscriber)
                {
                    if (strtolower($student->email) == strtolower($subscriber->email))
                    {
                        $exist = true;
                        break 1;
                    }
                }

                // if they do not exist, create them
                if ($exist == false)
                {

                    dd($student->email);
                    $new_subscriber = [
                        'email' => $student->email,
                        'name' => ucwords($student->firstname),
                        'fields' => [
                            'last_name' => ucwords($student->lastname),
                            'birthdate' => $student->birthdate ?? ''
                        ]
                        ];
                
                    $created_subscriber = $subscribersApi->create($new_subscriber);
                    var_dump($created_subscriber);
                }

                if(!isset($created_subscriber)) {
                    $created_subscriber = $subscribersApi->find($student->email);
                }

                // add them to the group.
                $addedSubscriber = $groupsApi->addSubscriber($target_group_id, $created_subscriber);
                var_dump($addedSubscriber);
            }
            sleep(1);


        }
    }





    private function createStudentSubscriber($student)
    {

        $groupsApi = (new \MailerLiteApi\MailerLite($this->api_key))->groups();

        $student_group_id = 8868042; // ETUDIANTS ACTUELS

        $subscriber = [
            'email' => $student->email,
            'name' => ucwords($student->firstname),
            'fields' => [
                'last_name' => ucwords($student->lastname),
                'birthdate' => $student->birthdate ?? ''
            ]
        ];

        $addedSubscriber = $groupsApi->addSubscriber($student_group_id, $subscriber); // returns added subscriber

        return $addedSubscriber;

    }


    private function createParentSubscriber($contact)
    {

        $groupsApi = (new \MailerLiteApi\MailerLite($this->api_key))->groups();

        $parent_group_id = 10862650; // PARENTS ACTUELS

        $subscriber = [
            'email' => $contact->email,
            'name' => ucwords($contact->firstname),
            'fields' => [
                'last_name' => ucwords($contact->lastname),
            ]
        ];

        $addedSubscriber = $groupsApi->addSubscriber($parent_group_id, $subscriber); // returns added subscriber

        return $addedSubscriber;

    }


    private function createAlumniSubscriber($contact)
    {

        $groupsApi = (new \MailerLiteApi\MailerLite($this->api_key))->groups();

        $alumni_group_id = 10862806; // ALUMNI

        $subscriber = [
            'email' => $contact->email,
            'name' => ucwords($contact->firstname),
            'fields' => [
                'last_name' => ucwords($contact->lastname),
            ]
        ];

        $groupsApi->addSubscriber($alumni_group_id, $subscriber); // returns added subscriber

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

        $enrollments = $period->enrollments;

        $groupsApi = (new \MailerLiteApi\MailerLite($this->api_key))->groups();


        // create subscribers from all students and associated contact in period

        foreach ($enrollments as $enrollment)
        {
            if ($enrollment->student->lead_type_id == 1) // converted
            {

                dump($this->createStudentSubscriber($enrollment->student));

                foreach ($enrollment->student->contacts as $contact)
                {
                    dump($this->createParentSubscriber($contact));
                }

            }

            if($enrollment->student->lead_type_id == 3) // dead
            {
                // unsubscribe
                $this->unsubscribeSubscriber($enrollment->student->email);

                foreach ($enrollment->student->contacts as $contact)
                {
                    dump($this->unsubscribeSubscriber($contact->email));
                }
            }

            if($enrollment->student->lead_type_id == 6) // exAlumno
            {
                // unsubscribe
                $this->unsubscribeSubscriber($enrollment->student->email);

                foreach ($enrollment->student->contacts as $contact)
                {
                    dump($this->unsubscribeSubscriber($contact->email));
                }
            }
        }


        // todo clean ETUDIANTS ACTUELS group - remove student whose status have changed.
        // but do not remove them right away, maybe wait a little


        
    }
}
