<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigratePhoneNumbers extends Command
{
    /**
     * The name and signature of the console command.
     * 
     * todo remove this method once the migration has been done
     *
     * @var string
     */
    protected $signature = 'datamigration:phone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate phone numbers from old DB';

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
     * todo filter duplicates.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = DB::table('afc2.users')
        ->select(DB::raw('telefono, celular, id'))
        ->where('role_id', 7) // filter students
        ->get();

        foreach ($users as $user)
        {
            if(isset($user->telefono))
            {
                $phone = new \App\Models\PhoneNumber;
                $phone->phone_number = $user->telefono;
                $phone->phoneable_type = Student::class;
                $phone->phoneable_id = $user->id;
                $phone->save();
            }

            if(isset($user->celular))
            {
                $phone = new \App\Models\PhoneNumber;
                $phone->phone_number = $user->celular;
                $phone->phoneable_type = Student::class;
                $phone->phoneable_id = $user->id;
                $phone->save();
            }
        }


        $users = DB::table('afc2.invoice_data')
        ->select(DB::raw('telefono, celular, id'))
        ->get();

        foreach ($users as $user)
        {
            if(isset($user->telefono))
            {
                $phone = new \App\Models\PhoneNumber;
                $phone->phone_number = $user->telefono;
                $phone->phoneable_type = 'App\Models\Contact';
                $phone->phoneable_id = $user->id;
                $phone->save();
            }

            if(isset($user->celular))
            {
                $phone = new \App\Models\PhoneNumber;
                $phone->phone_number = $user->celular;
                $phone->phoneable_type = 'App\Models\Contact';
                $phone->phoneable_id = $user->id;
                $phone->save();
            }
        }

    }
}
