<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;

class syncLeadStatuses extends Command
{
    protected $signature = 'academico:resync-statuses';

    protected $description = 'Student have a computed status, which is stored in the DB for performance reasons. In case statuses get out of sync, this command will resync them.';

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
