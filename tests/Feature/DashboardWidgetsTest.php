<?php

namespace Tests\Feature;

use App\Filament\Widgets\DailyOverview;
use App\Filament\Widgets\PeriodInfo;
use App\Filament\Widgets\StatsOverview;
use App\Models\Period;
use App\Models\Teacher;
use App\Models\User;
use App\Services\Settings;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var list<class-string>
     */
    private array $widgets = [StatsOverview::class, DailyOverview::class, PeriodInfo::class];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);
        app(Settings::class)->set(['current_period' => Period::factory()->create()->id]);
    }

    public function test_teachers_and_viewers_do_not_see_the_institution_widgets(): void
    {
        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');

        foreach ([Teacher::factory()->create()->user, $viewer] as $user) {
            $this->actingAs($user);

            foreach ($this->widgets as $widget) {
                $this->assertFalse($widget::canView(), "{$widget} is shown to user {$user->id}.");
            }

            $response = $this->followingRedirects()->get('/admin')->assertSuccessful();
            foreach ($this->widgets as $widget) {
                $response->assertDontSeeLivewire($widget);
            }
        }
    }

    public function test_secretaries_and_admins_see_the_widgets(): void
    {
        foreach (['secretary', 'admin'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->actingAs($user);

            foreach ($this->widgets as $widget) {
                $this->assertTrue($widget::canView(), "{$widget} is hidden from the {$role}.");
            }
        }
    }
}
