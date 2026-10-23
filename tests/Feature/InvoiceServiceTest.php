<?php

namespace Tests\Feature;

use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Invoices\Pages\ViewInvoice;
use App\Mail\InvoiceBulkDownload;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceType;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_is_built_from_institution_config(): void
    {
        config([
            'academico.company_name' => 'Example School',
            'academico.company_address' => '1 Main Street',
            'academico.company_id' => 'TAX-123',
            'academico.company_phone' => '+1 555 0100',
        ]);

        $generated = app(InvoiceService::class)->make($this->createInvoice());

        $this->assertSame('Example School', $generated->seller->name);
        $this->assertSame('1 Main Street', $generated->seller->address);
        $this->assertSame('TAX-123', $generated->seller->vat);
        $this->assertSame('+1 555 0100', $generated->seller->phone);
        $this->assertSame([], $generated->seller->custom_fields);
    }

    public function test_buyer_is_built_from_invoice_client_data(): void
    {
        $invoice = $this->createInvoice([
            'client_name' => 'Jane Doe',
            'client_idnumber' => 'ID-42',
            'client_address' => '2 High Street',
            'client_email' => 'jane@example.com',
        ]);

        $generated = app(InvoiceService::class)->make($invoice);

        $this->assertSame('Jane Doe', $generated->buyer->name);
        $this->assertSame('ID-42', $generated->buyer->vat);
        $this->assertSame('2 High Street', $generated->buyer->address);
        $this->assertSame([__('Email') => 'jane@example.com'], $generated->buyer->custom_fields);
    }

    public function test_buyer_email_is_omitted_when_empty(): void
    {
        $generated = app(InvoiceService::class)->make($this->createInvoice(['client_email' => null]));

        $this->assertSame([], $generated->buyer->custom_fields);
    }

    public function test_enrollment_line_description_is_translated(): void
    {
        app()->setLocale('fr');

        $course = Course::factory()->create(['name' => 'B1 Morning']);
        $invoice = $this->createInvoiceForCourse($course);

        $description = app(InvoiceService::class)->make($invoice)->items->first()->description;

        $this->assertStringContainsString('Cours: B1 Morning', $description);
        $this->assertStringContainsString('Période: '.$course->period->name, $description);
        $this->assertStringContainsString('Niveau: '.$course->level->name, $description);
    }

    public function test_download_is_in_the_institution_language(): void
    {
        config(['academico.locale' => 'fr']);
        app()->setLocale('en');
        $invoice = $this->createInvoiceForCourse(Course::factory()->create(['name' => 'B1 Morning']));

        $rendered = [];
        View::composer('invoices::templates.*', function ($view) use (&$rendered): void {
            $rendered = ['locale' => app()->getLocale(), 'description' => $view->getData()['invoice']->items->first()->description];
        });

        $response = app(InvoiceService::class)->download($invoice);

        $this->assertSame('fr', $rendered['locale']);
        $this->assertStringContainsString('Cours: B1 Morning', $rendered['description']);
        $this->assertSame('en', app()->getLocale());
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('invoice-'.$invoice->invoice_reference.'.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_invoice_page_downloads_the_pdf(): void
    {
        $invoice = $this->createInvoiceForCourse(Course::factory()->create());
        $this->actingAs(User::factory()->create()->assignRole(Role::findOrCreate('admin', 'web')));

        Livewire::test(ViewInvoice::class, ['record' => $invoice->id])
            ->callAction('download_pdf')
            ->assertFileDownloaded('invoice-'.$invoice->invoice_reference.'.pdf');

        Livewire::test(ListInvoices::class)
            ->callTableAction('download_pdf', $invoice)
            ->assertFileDownloaded('invoice-'.$invoice->invoice_reference.'.pdf');
    }

    public function test_footer_comes_from_the_settings(): void
    {
        config(['invoicing.invoice_footer' => 'Thank you for your payment']);

        $generated = app(InvoiceService::class)->make($this->createInvoice());

        $this->assertSame('Thank you for your payment', $generated->footer);
    }

    public function test_logo_comes_from_the_public_disk(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('logo.png')->store('settings', 'public');
        config(['academico.logo' => $path]);

        $generated = app(InvoiceService::class)->make($this->createInvoice());

        $this->assertSame(Storage::disk('public')->path($path), $generated->logo);
    }

    public function test_missing_logo_file_is_ignored(): void
    {
        Storage::fake('public');
        config(['academico.logo' => 'settings/missing.png']);

        $generated = app(InvoiceService::class)->make($this->createInvoice());

        $this->assertEmpty($generated->logo);
    }

    public function test_bulk_download_email_is_translated(): void
    {
        app()->setLocale('fr');

        (new InvoiceBulkDownload('invoices.zip'))
            ->assertSeeInHtml('Téléchargement de factures')
            ->assertSeeInHtml('Veuillez trouver ci-joint le fichier que vous avez demandé.');
    }

    private function createInvoiceForCourse(Course $course): Invoice
    {
        $enrollment = Enrollment::factory()->create(['course_id' => $course->id]);
        $invoice = $this->createInvoice();

        InvoiceDetail::create([
            'invoice_id' => $invoice->id,
            'product_name' => $course->name,
            'product_id' => $enrollment->id,
            'product_type' => Enrollment::class,
            'price' => 100,
            'quantity' => 1,
        ]);

        return $invoice;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createInvoice(array $attributes = []): Invoice
    {
        return Invoice::factory()->create([
            'invoice_type_id' => InvoiceType::factory()->create()->id,
            'date' => now(),
            ...$attributes,
        ]);
    }
}
