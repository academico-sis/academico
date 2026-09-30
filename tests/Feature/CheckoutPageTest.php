<?php

namespace Tests\Feature;

use App\Filament\Pages\CheckoutPage;
use App\Models\Config;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\InvoiceType;
use App\Models\Paymentmethod;
use App\Models\Period;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CheckoutPageTest extends TestCase
{
    use RefreshDatabase;

    private Period $period;

    private Course $course;

    private Enrollment $enrollment;

    private InvoiceType $invoiceType;

    private Paymentmethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Cancelled'])],
        ]);

        $year = Year::factory()->create();
        $this->period = Period::factory()->create(['year_id' => $year->id]);
        Config::where('name', 'current_period')->update(['value' => $this->period->id]);

        $this->course = Course::factory()->create([
            'period_id' => $this->period->id,
            'price' => 100,
        ]);

        $student = Student::factory()->create();

        $this->enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $this->course->id,
            'status_id' => 1,
        ]);

        $this->invoiceType = InvoiceType::factory()->create();
        $this->paymentMethod = Paymentmethod::factory()->create();

        Permission::findOrCreate('enrollments.edit', 'web');
        Permission::findOrCreate('enrollments.view', 'web');
        $role = Role::findOrCreate('admin', 'web');
        $role->givePermissionTo(['enrollments.edit', 'enrollments.view']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_page_loads_with_enrollment_id(): void
    {
        $response = $this->get(CheckoutPage::getUrl(['enrollment_id' => $this->enrollment->id]));
        $response->assertSuccessful();
    }

    public function test_page_returns_404_without_enrollment_id(): void
    {
        $response = $this->get(CheckoutPage::getUrl());
        $response->assertNotFound();
    }

    public function test_products_are_prepopulated_from_enrollment(): void
    {
        $component = Livewire::withQueryParams(['enrollment_id' => $this->enrollment->id])
            ->test(CheckoutPage::class);

        $data = $component->get('data');
        $products = $data['products'] ?? [];

        $enrollmentProduct = collect($products)->firstWhere('product_type', Enrollment::class);
        $this->assertNotNull($enrollmentProduct);
        $this->assertEquals($this->enrollment->id, $enrollmentProduct['product_id']);
    }

    public function test_submit_creates_invoice_and_payment(): void
    {
        $component = Livewire::withQueryParams(['enrollment_id' => $this->enrollment->id])
            ->test(CheckoutPage::class);

        $component->set('data.products', [
            [
                'product_name' => $this->course->name,
                'product_code' => '',
                'product_type' => Enrollment::class,
                'product_id' => $this->enrollment->id,
                'price' => 100,
                'quantity' => 1,
                'comment' => '',
            ],
        ]);

        $component->set('data.payments', [
            ['payment_method' => $this->paymentMethod->code, 'value' => 100, 'date' => now()->format('Y-m-d')],
        ]);

        $component->set('data.client_name', 'Test Client');
        $component->set('data.invoice_type_id', $this->invoiceType->id);
        $component->set('data.date', now()->format('Y-m-d'));

        $component->call('submit');

        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseHas('invoices', ['client_name' => 'Test Client']);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', ['value' => 10000]);
    }

    public function test_enrollment_marked_paid_after_full_payment(): void
    {
        $component = Livewire::withQueryParams(['enrollment_id' => $this->enrollment->id])
            ->test(CheckoutPage::class);

        $component->set('data.products', [
            [
                'product_name' => $this->course->name,
                'product_code' => '',
                'product_type' => Enrollment::class,
                'product_id' => $this->enrollment->id,
                'price' => 100,
                'quantity' => 1,
                'comment' => '',
            ],
        ]);

        $component->set('data.payments', [
            ['payment_method' => $this->paymentMethod->code, 'value' => 100, 'date' => now()->format('Y-m-d')],
        ]);

        $component->set('data.client_name', 'Test Client');
        $component->set('data.invoice_type_id', $this->invoiceType->id);
        $component->set('data.date', now()->format('Y-m-d'));

        $component->call('submit');

        $this->enrollment->refresh();
        $this->assertEquals(2, $this->enrollment->status_id);
    }

    public function test_checkout_button_visible_on_unpaid_enrollment(): void
    {
        $this->get(route('filament.admin.resources.enrollments.view', $this->enrollment))
            ->assertSuccessful()
            ->assertSee(__('Checkout enrollment'));
    }

    public function test_checkout_button_hidden_on_paid_enrollment(): void
    {
        $this->enrollment->update(['status_id' => 2]);

        $this->get(route('filament.admin.resources.enrollments.view', $this->enrollment))
            ->assertSuccessful()
            ->assertDontSee(__('Checkout enrollment'));
    }

    public function test_accounting_called_when_toggle_on(): void
    {
        config(['invoicing.invoicing_system' => 'ecuasolutions']);
        config(['invoicing.ecuasolutions.url' => 'https://mock-accounting.test/api']);
        config(['invoicing.ecuasolutions.ping_url' => 'https://mock-accounting.test/ping']);
        config(['invoicing.ecuasolutions.key' => 'test-key']);

        Http::fake([
            'mock-accounting.test/*' => Http::response(['mensaje' => 'REC-001'], 200),
        ]);

        $component = Livewire::withQueryParams(['enrollment_id' => $this->enrollment->id])
            ->test(CheckoutPage::class);

        $component->set('data.products', [
            [
                'product_name' => $this->course->name,
                'product_code' => 'COURSE01',
                'product_type' => Enrollment::class,
                'product_id' => $this->enrollment->id,
                'price' => 100,
                'quantity' => 1,
                'comment' => '',
            ],
        ]);

        $component->set('data.payments', [
            ['payment_method' => $this->paymentMethod->code, 'value' => 100, 'date' => now()->format('Y-m-d')],
        ]);

        $component->set('data.client_name', 'Test Client');
        $component->set('data.client_idnumber', '1234567890');
        $component->set('data.invoice_type_id', $this->invoiceType->id);
        $component->set('data.date', now()->format('Y-m-d'));
        $component->set('data.send_to_accounting', true);

        $component->call('submit');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'mock-accounting.test/api'));
        $this->assertDatabaseHas('invoices', ['receipt_number' => 'REC-001']);
    }

    public function test_accounting_not_called_when_toggle_off(): void
    {
        config(['invoicing.invoicing_system' => 'ecuasolutions']);
        config(['invoicing.ecuasolutions.url' => 'https://mock-accounting.test/api']);
        config(['invoicing.ecuasolutions.ping_url' => 'https://mock-accounting.test/ping']);
        config(['invoicing.ecuasolutions.key' => 'test-key']);

        Http::fake();

        $component = Livewire::withQueryParams(['enrollment_id' => $this->enrollment->id])
            ->test(CheckoutPage::class);

        $component->set('data.products', [
            [
                'product_name' => $this->course->name,
                'product_code' => '',
                'product_type' => Enrollment::class,
                'product_id' => $this->enrollment->id,
                'price' => 100,
                'quantity' => 1,
                'comment' => '',
            ],
        ]);

        $component->set('data.payments', [
            ['payment_method' => $this->paymentMethod->code, 'value' => 100, 'date' => now()->format('Y-m-d')],
        ]);

        $component->set('data.client_name', 'Test Client');
        $component->set('data.invoice_type_id', $this->invoiceType->id);
        $component->set('data.date', now()->format('Y-m-d'));
        $component->set('data.send_to_accounting', false);

        $component->call('submit');

        Http::assertNothingSent();
        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_accounting_not_called_when_internal_system(): void
    {
        config(['invoicing.invoicing_system' => 'internal']);

        Http::fake();

        $component = Livewire::withQueryParams(['enrollment_id' => $this->enrollment->id])
            ->test(CheckoutPage::class);

        $component->set('data.products', [
            [
                'product_name' => $this->course->name,
                'product_code' => '',
                'product_type' => Enrollment::class,
                'product_id' => $this->enrollment->id,
                'price' => 100,
                'quantity' => 1,
                'comment' => '',
            ],
        ]);

        $component->set('data.payments', [
            ['payment_method' => $this->paymentMethod->code, 'value' => 100, 'date' => now()->format('Y-m-d')],
        ]);

        $component->set('data.client_name', 'Test Client');
        $component->set('data.invoice_type_id', $this->invoiceType->id);
        $component->set('data.date', now()->format('Y-m-d'));

        $component->call('submit');

        Http::assertNothingSent();
        $this->assertDatabaseCount('invoices', 1);
    }
}
