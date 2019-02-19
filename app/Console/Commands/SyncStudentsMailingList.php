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



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $period = Period::get_default_period();

        $current_students = $period->enrollments;

        $student_group_id = 8868042; // ETUDIANTS ACTUELS

        $groupsApi = (new \MailerLiteApi\MailerLite($this->api_key))->groups();


        //$addedSubscribers = $groupsApi->importSubscribers($student_group_id, $subscribers, $options); // returns imported subscribers divided into groups by import status


        // enroll all converted students to this group


        $this->add_users_to_group($current_students, $student_group_id);


        //$this->clean_users_from_group($current_students, $student_group_id, $api_key);
        
       /*  // and another time with parents data
        $current_parents = $this->users_model->get_current_students_parents($period);
        $parent_group_id = 10862650; // PARENTS ACTUELS
        $this->add_users_to_group($current_parents, $parent_group_id, $api_key);
        $this->clean_users_from_group($current_parents, $parent_group_id, $api_key); */
        
    }
}
