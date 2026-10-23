<?php

namespace Tests\Feature;

use App\Events\ExternalCoursesReportEvent;
use App\Events\MonthlyReportEvent;
use App\Services\Settings;
use Illuminate\Console\Scheduling\Event as ScheduledEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ScheduledTasksTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, string, class-string}>
     */
    public static function settingGatedTasks(): array
    {
        return [
            'external courses report' => ['external-courses-report', 'settings.external_courses_report', ExternalCoursesReportEvent::class],
            'monthly report' => ['monthly-report', 'settings.monthly_report', MonthlyReportEvent::class],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function taskSettings(): array
    {
        return array_map(fn (array $task): array => [$task[0], $task[1]], self::settingGatedTasks());
    }

    #[DataProvider('taskSettings')]
    public function test_task_is_scheduled_when_its_setting_is_off_at_boot(string $taskName, string $settingKey): void
    {
        $this->assertFalse(config($settingKey));
        $this->assertNotNull($this->scheduledTask($taskName));
    }

    #[DataProvider('settingGatedTasks')]
    public function test_task_does_nothing_when_its_setting_is_off(string $taskName, string $settingKey, string $eventClass): void
    {
        Event::fake([$eventClass]);
        app(Settings::class)->set([$settingKey => false]);

        $this->scheduledTask($taskName)->run($this->app);

        Event::assertNotDispatched($eventClass);
    }

    #[DataProvider('settingGatedTasks')]
    public function test_task_runs_when_its_setting_is_on(string $taskName, string $settingKey, string $eventClass): void
    {
        Event::fake([$eventClass]);
        app(Settings::class)->set([$settingKey => true]);

        $this->scheduledTask($taskName)->run($this->app);

        Event::assertDispatched($eventClass);
    }

    private function scheduledTask(string $name): ?ScheduledEvent
    {
        return collect($this->app->make(Schedule::class)->events())
            ->first(fn (ScheduledEvent $event): bool => $event->description === $name);
    }
}
