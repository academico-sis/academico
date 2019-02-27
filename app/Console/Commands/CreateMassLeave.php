<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Teacher;
use Illuminate\Console\Command;

class CreateMassLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:masscreate';

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

        // todo move to UI
        $dates = [
            0 => Carbon::parse('2019-03-02'),
            1 => Carbon::parse('2019-03-04'),
            2 => Carbon::parse('2019-03-05'),
        ];

        foreach (Teacher::all() as $teacher)
        {
            foreach ($dates as $date)
            {               
                $teacher->leaves()->create([
                    'date' => $date->toDateString(),
                    'leave_type_id' => 1,
                ]);
            }
        }
    }
}
