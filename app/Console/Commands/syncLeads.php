<?php

namespace App\Console\Commands;

use App\Models\Period;
use App\Models\Student;
use App\Models\Enrollment;
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
        $period = Period::find(23);

        // get all past enrollments
        $leads = Enrollment::whereHas('course', function($q) use ($period) {
            return $q->where('period_id', '<', $period->id);
        })->get();

        // mark every student without a lead status as oldStudent
        foreach($leads as $lead)
        {
            if ($lead->student->lead_type_id == null) {
                $lead->student->update([
                    'lead_type_id' => 7
                ]);
            }
        }


        }
}
