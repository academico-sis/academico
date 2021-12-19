<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;

class migrateLeadStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leadstatus:migrate';

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
     * @return int
     */
    public function handle()
    {
        foreach (Student::where('lead_type_id', 1)->get() as $student) {
            $student->update(['lead_type_id' => null]);
        }

        foreach (Student::enrolled()->get() as $student) {
            $student->update(['lead_type_id' => 1]);
        }

        return Command::SUCCESS;
    }
}
