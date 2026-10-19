<?php

namespace Database\Seeders;

use App\Models\AttendanceType;
use App\Models\Campus;
use App\Models\ContactRelationship;
use App\Models\EnrollmentStatusType;
use App\Models\EvaluationType;
use App\Models\LeaveType;
use App\Models\Paymentmethod;
use App\Models\ResultType;
use App\Models\Skills\SkillScale;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCampuses();
        $this->seedEnrollmentStatusTypes();
        $this->seedResultTypes();
        $this->seedEvaluationTypes();
        $this->seedAttendanceTypes();
        $this->seedContactRelationships();
        $this->seedSkillScales();
        $this->seedLeaveTypes();
        $this->seedPaymentMethods();
    }

    protected function seedCampuses(): void
    {
        Campus::create([
            'id' => 1,
            'name' => [
                'en' => 'Internal',
                'es' => 'Interno',
                'fr' => 'Interne',
            ],
        ]);
    }

    protected function seedEnrollmentStatusTypes(): void
    {
        EnrollmentStatusType::create([
            'id' => 1,
            'name' => [
                'es' => 'PENDIENTE',
                'en' => 'PENDING',
                'fr' => 'NON-PAYÉ',
            ],
        ]);

        EnrollmentStatusType::create([
            'id' => 2,
            'name' => [
                'es' => 'PAGADA',
                'en' => 'PAID',
                'fr' => 'PAYÉ',
            ],
        ]);

        EnrollmentStatusType::create([
            'id' => 3,
            'name' => [
                'es' => 'ANULADA',
                'en' => 'CANCELED',
                'fr' => 'ANNULÉ',
            ],
        ]);

        EnrollmentStatusType::create([
            'id' => 4,
            'name' => [
                'es' => 'TRASPASO',
                'en' => 'TRANSFERED',
                'fr' => 'TRANSFÉRÉ',
            ],
        ]);

        EnrollmentStatusType::create([
            'id' => 5,
            'name' => [
                'es' => 'DEVOLUCION',
                'en' => 'REFUND',
                'fr' => 'REMBOURSÉ',
            ],
        ]);
    }

    protected function seedResultTypes(): void
    {
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
            ],
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
            ],
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
            ],
        ]);
    }

    protected function seedEvaluationTypes(): void
    {
        EvaluationType::create([
            'id' => 1,
            'name' => 'NOTES',
        ]);

        EvaluationType::create([
            'id' => 2,
            'name' => 'COMPÉTENCES',
        ]);
    }

    protected function seedAttendanceTypes(): void
    {
        AttendanceType::create([
            'id' => 1,
            'name' => ['fr' => 'PRÉSENT(E)', 'es' => 'PRESENTE', 'en' => 'PRESENT'],
            'class' => 'success',
            'color' => '#00C851',
            'icon' => '<i class="la la-user"></i>',
        ]);

        AttendanceType::create([
            'id' => 2,
            'name' => ['fr' => 'PRÉSENCE PARTIELLE', 'es' => 'PRESENCIA PARCIAL', 'en' => 'PARTIAL PRESENCE'],
            'class' => 'warning',
            'color' => '#FFBB33',
            'icon' => '<i class="la la-clock-o"></i>',
        ]);

        AttendanceType::create([
            'id' => 3,
            'name' => ['fr' => 'EXCUSÉ(E)', 'es' => 'JUSTIFICADO', 'en' => 'EXCUSED'],
            'class' => 'info',
            'color' => '#33B5E5',
            'icon' => '<i class="la la-exclamation"></i>',
        ]);

        AttendanceType::create([
            'id' => 4,
            'name' => ['fr' => 'ABSENT(E)', 'es' => 'AUSENTE', 'en' => 'ABSENT'],
            'class' => 'danger',
            'color' => '#ff4444',
            'icon' => '<i class="la la-user-times"></i>',
        ]);
    }

    protected function seedContactRelationships(): void
    {
        ContactRelationship::create([
            'id' => 1,
            'name' => ['fr' => 'FAMILLE', 'es' => 'FAMILIA', 'en' => 'FAMILY'],
        ]);

        ContactRelationship::create([
            'id' => 2,
            'name' => ['fr' => 'TRAVAIL', 'es' => 'TRABAJO', 'en' => 'WORK'],
        ]);
    }

    protected function seedSkillScales(): void
    {
        SkillScale::create([
            'id' => 1,
            'shortname' => ['fr' => 'NON', 'es' => 'NO', 'en' => 'NO'],
            'name' => ['fr' => 'NON-ACQUIS', 'es' => 'NO ADQUIRIDO', 'en' => 'NOT ACQUIRED'],
            'value' => 0,
        ]);

        SkillScale::create([
            'id' => 2,
            'shortname' => ['fr' => 'EC', 'es' => 'EC', 'en' => 'WIP'],
            'name' => ['fr' => 'EN COURS', 'es' => 'EN CURSO DE ADQUISICIÓN', 'en' => 'IN PROGRESS'],
            'value' => 0.4,
        ]);

        SkillScale::create([
            'id' => 3,
            'shortname' => ['fr' => 'OUI', 'es' => 'SI', 'en' => 'YES'],
            'name' => ['fr' => 'ACQUIS', 'es' => 'ADQUIRIDO', 'en' => 'ACQUIRED'],
            'value' => 1,
        ]);
    }

    protected function seedLeaveTypes(): void
    {
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
    }

    protected function seedPaymentMethods(): void
    {
        Paymentmethod::create(['id' => 1, 'name' => 'Credit Card', 'code' => 'TC']);
        Paymentmethod::create(['id' => 2, 'name' => 'Cash', 'code' => 'EFECT']);
    }
}
