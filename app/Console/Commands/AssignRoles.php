<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datamigration:assignroles';

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
         // get admins
        $users = DB::table('afc2.users')
        ->select(DB::raw('id, email'))
        ->where('role_id', 1)
        ->get();

        foreach ($users as $user)
        {
            User::find($user->id)->assignRole('admin');
            echo "Role ADMIN assigned to " . $user->email . "\n";
        }


        // get ex teachers
        $users = DB::table('afc2.users')
        ->select(DB::raw('id, email'))
        ->where('role_id', 4)
        ->get();

        foreach ($users as $user)
        {
            $u = Teacher::find($user->id);
            $u->deleted_at = "2018-01-01";
            $u->user()->deleted_at = "2018-01-01";
            $u->save();
            echo "DELETED TEACHER " . $user->email . "\n";
        }

        // get administrative workers
        $users = DB::table('afc2.users')
        ->select(DB::raw('id, email'))
        ->where('role_id', 9)
        ->get();

        foreach ($users as $user)
        {
            User::find($user->id)->assignRole('secretary');
            echo "Role SECRETARY assigned to " . $user->email . "\n";
        }

        
        foreach (Teacher::all() as $teacher)
        {
            $user = User::find($teacher->user_id);
            $user->givePermissionTo('evaluation.edit');
            $user->givePermissionTo('attendance.edit');
            $user->givePermissionTo('evaluation.view');
            $user->givePermissionTo('calendars.view');
            $user->givePermissionTo('hr.view');


        }

    }
}
