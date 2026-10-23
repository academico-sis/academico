<?php

namespace Tests\Feature;

use App\Filament\Resources\Partners\Pages\ManagePartners;
use App\Models\Partner;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PartnerResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['settings.external_courses_enabled' => true]);

        $role = Role::findOrCreate('secretary', 'web');
        foreach (['courses.view', 'courses.edit'] as $permission) {
            $role->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs(User::factory()->create()->assignRole('secretary'));
    }

    public function test_partners_are_listed_with_their_report_day(): void
    {
        $partner = Partner::factory()->create(['name' => 'City Business School', 'send_report_on' => 12]);

        Livewire::test(ManagePartners::class)
            ->assertCanSeeTableRecords([$partner])
            ->assertSee('City Business School');
    }

    public function test_partner_is_created_with_a_name_and_a_report_day(): void
    {
        Livewire::test(ManagePartners::class)
            ->callAction(TestAction::make(CreateAction::getDefaultName()), ['name' => 'City Business School', 'send_report_on' => 12])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('partners', ['name' => 'City Business School', 'send_report_on' => 12]);
    }
}
