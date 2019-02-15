<?php

namespace App\Console\Commands;

use App\Models\Period;
use App\Models\Student;
use Illuminate\Console\Command;

class syncLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:sync';

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $period = Period::find(22);
        
        // for each enrollment in current period,
        foreach($period->enrollments as $enrollment)
        {
            // mark the status as converted
            $enrollment->student()->update([
                'lead_type_id' => 1
            ]);
        }
/*         
        $value = $period->id;
        
        foreach(Student::where('lead_type_id', 1)->get() as $converted)
        {
            if ($converted->enrollments->where('course', function ($q) use ($value) {
                    $q->where('period_id', $value);
                })->count() == 0)
            {
                $converted->update([
                    'lead_type_id' => 4
                ]);
                echo 'a';
            }

        } */
        // TODO for each converted student who is not enrolled,

        // remove the converted status

    }
}
