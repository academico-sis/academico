<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\ScheduledPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledPaymentBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
        ]);
    }

    public function test_is_paid_returns_true_for_status_2(): void
    {
        $sp = ScheduledPayment::factory()->create(['status' => 2]);

        $this->assertTrue($sp->isPaid());
    }

    public function test_is_paid_returns_false_for_status_1(): void
    {
        $sp = ScheduledPayment::factory()->create(['status' => 1]);

        $this->assertFalse($sp->isPaid());
    }

    public function test_mark_as_paid_sets_status_to_2(): void
    {
        $sp = ScheduledPayment::factory()->create(['status' => 1]);

        $sp->markAsPaid();

        $this->assertEquals(2, $sp->fresh()->status);
    }

    public function test_status_scope_filters_paid(): void
    {
        $paid = ScheduledPayment::factory()->create(['status' => 2]);
        $pending = ScheduledPayment::factory()->create(['status' => 1]);

        $results = ScheduledPayment::query()->status('2')->pluck('id');

        $this->assertTrue($results->contains($paid->id));
        $this->assertFalse($results->contains($pending->id));
    }

    public function test_status_scope_filters_pending(): void
    {
        $paid = ScheduledPayment::factory()->create(['status' => 2]);
        $pending = ScheduledPayment::factory()->create(['status' => 1]);

        $results = ScheduledPayment::query()->status('1')->pluck('id');

        $this->assertFalse($results->contains($paid->id));
        $this->assertTrue($results->contains($pending->id));
    }

    public function test_status_scope_default_returns_all(): void
    {
        $paid = ScheduledPayment::factory()->create(['status' => 2]);
        $pending = ScheduledPayment::factory()->create(['status' => 1]);

        $results = ScheduledPayment::query()->status('all')->pluck('id');

        $this->assertTrue($results->contains($paid->id));
        $this->assertTrue($results->contains($pending->id));
    }

    public function test_status_type_name_returns_paid_label(): void
    {
        $sp = ScheduledPayment::factory()->create(['status' => 2]);

        $this->assertEquals(__('Paid'), $sp->status_type_name);
    }

    public function test_status_type_name_returns_pending_label(): void
    {
        $sp = ScheduledPayment::factory()->create(['status' => 1]);

        $this->assertEquals(__('Pending'), $sp->status_type_name);
    }

    public function test_save_scheduled_payments_creates_new(): void
    {
        $enrollment = Enrollment::factory()->create();

        $payments = collect([
            (object) ['id' => null, 'date' => '2025-06-01', 'value' => 50, 'status' => 1],
            (object) ['id' => null, 'date' => '2025-07-01', 'value' => 50, 'status' => 1],
        ]);

        $enrollment->saveScheduledPayments($payments);

        $this->assertEquals(2, $enrollment->scheduledPayments()->count());
    }

    public function test_save_scheduled_payments_deletes_removed(): void
    {
        $enrollment = Enrollment::factory()->create();
        $existing = ScheduledPayment::factory()->create([
            'enrollment_id' => $enrollment->id,
            'status' => 1,
        ]);

        // Pass empty collection — the existing one should be deleted
        $enrollment->saveScheduledPayments(collect([]));

        $this->assertEquals(0, $enrollment->scheduledPayments()->count());
    }

    public function test_save_scheduled_payments_mixed_create_update_delete(): void
    {
        $enrollment = Enrollment::factory()->create();

        $keep = ScheduledPayment::factory()->create([
            'enrollment_id' => $enrollment->id,
            'date' => '2025-06-01',
            'value' => 50,
            'status' => 1,
        ]);
        $delete = ScheduledPayment::factory()->create([
            'enrollment_id' => $enrollment->id,
            'date' => '2025-07-01',
            'value' => 60,
            'status' => 1,
        ]);

        $payments = collect([
            (object) ['id' => $keep->id, 'date' => '2025-06-15', 'value' => 55, 'status' => 1],
            (object) ['id' => null, 'date' => '2025-08-01', 'value' => 70, 'status' => 1],
        ]);

        $enrollment->saveScheduledPayments($payments);

        $remaining = $enrollment->scheduledPayments()->get();
        $this->assertEquals(2, $remaining->count());
        $this->assertNull(ScheduledPayment::find($delete->id));
    }
}
