<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Campus;
use App\Models\EnrollmentStatusType;
use App\Models\ResultType;
use App\Models\EvaluationType;
use App\Models\AttendanceType;
use App\Models\ContactRelationship;
use App\Models\Skills\SkillScale;
use App\Models\LeaveType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //factory(App\Models\User::class, 20)->create();
        //factory(App\Models\Period::class, 1)->create();
        //factory(App\Models\Course::class, 5)->create();
        
        Campus::create([
            'id' => 1,
            'name' => [
               'en' => 'Internal',
               'es' => 'Interno',
               'fr' => 'Interne',
            ],
         ]);

                 
        Campus::create([
            'id' => 2,
            'name' => [
               'en' => 'External',
               'es' => 'Externo',
               'fr' => 'Externe',
            ],
         ]);

         EnrollmentStatusType::create([
            'id' => 1,
            'name' => [
                'es' => 'PENDIENTE',
                'en' => 'PENDING',
                'fr' => 'NON-PAYÉ',
                ]
         ]);

         EnrollmentStatusType::create([
            'id' => 2,
            'name' => [
                'es' => 'PAGADA',
                'en' => 'PAID',
                'fr' => 'PAYÉ',
                ]
         ]);

         EnrollmentStatusType::create([
            'id' => 3,
            'name' => [
                'es' => 'ANULADA',
                'en' => 'CANCELED',
                'fr' => 'ANNULÉ',
                ]
         ]);

         EnrollmentStatusType::create([
            'id' => 4,
            'name' => [
                'es' => 'TRASPASO',
                'en' => 'TRANSFERED',
                'fr' => 'TRANSFÉRÉ',
                ]
         ]);

         EnrollmentStatusType::create([
            'id' => 5,
            'name' => [
                'es' => 'DEVOLUCION',
                'en' => 'REFUND',
                'fr' => 'REMBOURSÉ',
                ]
         ]);



         ResultType::create([
            'id' => 1,
            'name' => [
                'fr' => 'VALIDÉ',
                'es' => 'APROBADO',
                'en' => 'PASS',

            ],
            'description' => [
                'fr' => 'Peut passer au niveau suivant',
                'es' => 'Puede pasar al nivel siguiente',
                'en' => 'May go to the next level',
                ]
         ]);

         ResultType::create([
            'id' => 2,
            'name' => [
                'fr' => 'NON-VALIDÉ',
                'es' => 'REPROBADO',
                'en' => 'FAIL',

            ],
            'description' => [
                'fr' => 'Ne peut pas passer au niveau suivant',
                'es' => 'No puede pasar al nivel siguiente',
                'en' => 'Cannot go to the next level',
                ]
         ]);

         ResultType::create([
            'id' => 3,
            'name' => [
                'fr' => 'VOIR COORD. PEDA',
                'es' => 'VER COORD. PEDA',
                'en' => 'SEE DIR.',

            ],
            'description' => [
                'fr' => 'Vérifier le résultat avec la direction pédagogique',
                'es' => 'Ver con la dirección pedagógica',
                'en' => 'Check results with the Pedagogy department',
                ]
         ]);


         EvaluationType::create([
            'id' => 1,
            'name' => [
                'fr' => 'NOTES',
                'es' => 'NOTAS',
                'en' => 'GRADES',
                ]
         ]);

         EvaluationType::create([
            'id' => 2,
            'name' => [
                'fr' => 'COMPÉTENCES',
                'es' => 'COMPETENCIAS',
                'en' => 'SKILLS',
                ]
         ]);


         AttendanceType::create([
            'id' => 1,
            'name' => ['fr' => 'PRÉSENT(E)', 'es' => 'PRESENTE', 'en' => 'PRESENT']
         ]);

         AttendanceType::create([
            'id' => 2,
            'name' => ['fr' => 'PRÉSENCE PARTIELLE', 'es' => 'PRESENCIA PARCIAL', 'en' => 'PARTIAL PRESENCE']
         ]);

         AttendanceType::create([
            'id' => 3,
            'name' => ['fr' => 'EXCUSÉ(E)', 'es' => 'JUSTIFICADO', 'en' => 'EXCUSED']
         ]);

         AttendanceType::create([
            'id' => 4,
            'name' => ['fr' => 'ABSENT(E)', 'es' => 'AUSENTE', 'en' => 'ABSENT']
         ]);


         ContactRelationship::create([
            'id' => 1,
            'name' => ['fr' => 'FAMILLE', 'es' => 'FAMILIA', 'en' => 'FAMILY']
         ]);

         ContactRelationship::create([
            'id' => 2,
            'name' => ['fr' => 'TRAVAIL', 'es' => 'TRABAJO', 'en' => 'WORK']
         ]);


         DB::table('fees')->insert(
            array(
                'id' => 1,
                'name' => 'Matricula',
                'price' => '20'
            )
        );

        SkillScale::create([
            'id' => 1,
            'shortname' => ['fr' => 'NON', 'es' => 'NO', 'en' => 'NO'],
            'name' => ['fr' => 'NON-ACQUIS', 'es' => 'NO ADQUIRIDO', 'en' => 'NOT ACQUIRED'],
            'value' => 0
        ]);

        SkillScale::create([
            'id' => 2,
            'shortname' => ['fr' => 'EC', 'es' => 'EC', 'en' => 'WIP'],
            'name' => ['fr' => 'EN COURS', 'es' => 'EN CURSO DE ADQUISICIÓN', 'en' => 'IN PROGRESS'],
            'value' => 0.4
        ]);

        SkillScale::create([
            'id' => 3,
            'shortname' => ['fr' => 'OUI', 'es' => 'SI', 'en' => 'YES'],
            'name' => ['fr' => 'ACQUIS', 'es' => 'ADQUIRIDO', 'en' => 'ACQUIRED'],
            'value' => 1
        ]);

        LeaveType::create([
            'id' => 1,
            'name' => ['fr' => 'JOUR FÉRIÉ', 'es' => 'FERIADO', 'en' => 'NATIONAL HOLIDAY'],
        ]);

        LeaveType::create([
            'id' => 2,
            'name' => ['fr' => 'CONGÉ', 'es' => 'VACACIONES', 'en' => 'LEAVE'],
        ]);

        LeaveType::create([
            'id' => 3,
            'name' => ['fr' => 'SPÉCIAL', 'es' => 'ESPECIAL', 'en' => 'SPECIAL'],
        ]);

        LeaveType::create([
            'id' => 4,
            'name' => ['fr' => 'RÉCUPÉRATION', 'es' => 'RECUPERACIÓN', 'en' => 'RECOVERY'],
        ]);

        LeaveType::create([
            'id' => 5,
            'name' => ['fr' => 'MALADIE', 'es' => 'ENFERMEDAD', 'en' => 'SICK LEAVE'],
        ]);


        // create required permissions
        
        // courses permissions
        Permission::create(['name' => 'courses.view']);
        Permission::create(['name' => 'courses.edit']);
        Permission::create(['name' => 'courses.delete']);
        
        // enrollments
        Permission::create(['name' => 'enrollments.view']);
        Permission::create(['name' => 'enrollments.create']);
        Permission::create(['name' => 'enrollments.edit']);
        Permission::create(['name' => 'enrollments.delete']);

        // attendance permissions
        Permission::create(['name' => 'attendance.view']);
        Permission::create(['name' => 'attendance.edit']);

        // grades, skills, etc.
        Permission::create(['name' => 'evaluation.edit']);
        Permission::create(['name' => 'evaluation.view']);

        // reports
        Permission::create(['name' => 'reports.view']);
        Permission::create(['name' => 'reports.edit']);

        // calendars
        Permission::create(['name' => 'calendars.view']);

        // HR
        Permission::create(['name' => 'hr.view']);
        Permission::create(['name' => 'hr.manage']);

        // student related permissions
        Permission::create(['name' => 'student.edit']);
        Permission::create(['name' => 'comments.edit']);

        // create core roles and assign permissions

        // admins have all permissions
        $role = Role::create(['name' => 'admin']);
        foreach (Permission::all() as $permission)
        {
            $role->givePermissionTo($permission->name);
        }
        
        // managers have all permissions but are NOT admins
        $role = Role::create(['name' => 'manager']);
        foreach (Permission::all() as $permission)
        {
            $role->givePermissionTo($permission->name);
        }

        // secretaries typically deal with enrollments, course information, etc.
        $role = Role::create(['name' => 'secretary']);
        $role->givePermissionTo('calendars.view');
        $role->givePermissionTo('evaluation.view');
        $role->givePermissionTo('attendance.view');
        $role->givePermissionTo('attendance.edit');
        $role->givePermissionTo('enrollments.view');
        $role->givePermissionTo('enrollments.edit');
        $role->givePermissionTo('enrollments.create');
        $role->givePermissionTo('courses.view');


        Permission::create(['name' => 'moodle.login']);

    }
}
