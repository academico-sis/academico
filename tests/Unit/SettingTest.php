<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_setting(): void
    {
        $setting = Setting::factory()->create();

        $this->assertDatabaseHas('settings', ['id' => $setting->id]);
    }

    public function test_value_keeps_its_type(): void
    {
        Setting::factory()->create(['key' => 'flag', 'value' => false]);
        Setting::factory()->create(['key' => 'number', 'value' => 12]);
        Setting::factory()->create(['key' => 'list', 'value' => ['a', 'b']]);

        $this->assertFalse(Setting::firstWhere('key', 'flag')->value);
        $this->assertSame(12, Setting::firstWhere('key', 'number')->value);
        $this->assertSame(['a', 'b'], Setting::firstWhere('key', 'list')->value);
    }
}
