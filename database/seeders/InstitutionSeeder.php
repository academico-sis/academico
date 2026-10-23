<?php

namespace Database\Seeders;

use App\Models\Period;
use App\Models\Year;
use Illuminate\Database\Seeder;

/**
 * What a new institution needs to start: the reference data, the roles and permissions, and a first
 * period, which the administrator then renames or replaces.
 */
class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ReferenceDataSeeder::class,
            PermissionsSeeder::class,
        ]);

        $year = Year::create(['name' => (string) now()->year]);

        Period::create([
            'name' => 'Default',
            'year_id' => $year->id,
            'start' => now()->toDateString(),
            'end' => now()->addMonths(3)->toDateString(),
            'order' => 1,
        ]);
    }
}
