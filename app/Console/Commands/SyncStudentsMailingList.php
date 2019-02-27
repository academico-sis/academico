<?php

namespace App\Console\Commands;

use App\Models\Period;
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
        
        $this->api_key = env('mailerlite_api_key');
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
                $this->createAlumniSubscriber($enrollment->student);

                foreach ($enrollment->student->contacts as $contact)
                {
                    dump($this->createAlumniSubscriber($contact));
                }
            }

            //sleep(1);
        }

    }
}
