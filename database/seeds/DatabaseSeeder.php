<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 20)->create();
        factory(App\Models\Period::class, 1)->create();
        factory(App\Models\Course::class, 5)->create();
        
        // create core roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'secretary']);
        Role::create(['name' => 'student']);

        // create required permissions
        
        // courses permissions
        Permission::create(['name' => 'view all courses']);
        Permission::create(['name' => 'edit a course']);
        Permission::create(['name' => 'delete a course']);
        
        // enrollments
        Permission::create(['name' => 'enroll a student']);
        Permission::create(['name' => 'perform extraordinary operations on enrollments']);
        Permission::create(['name' => 'edit an enrollment']);
        Permission::create(['name' => 'view enrollment details']);
        Permission::create(['name' => 'cancel an enrollment']);

        // grades
        Permission::create(['name' => 'edit grades']);
        Permission::create(['name' => 'view grades']);
        Permission::create(['name' => 'edit course result']);
        Permission::create(['name' => 'view course result']);

        // reports
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'edit reports details']);      
    }
}
