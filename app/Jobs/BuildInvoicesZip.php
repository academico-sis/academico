<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Ramsey\Uuid\Uuid;
use ZipArchive;

class BuildInvoicesZip implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 500;

    /**
     * Create a new job instance.
     *
     * @param string[] $entries
     * @return void
     */
    public function __construct(
        public array $entries,
        public string $email,
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $folder = Uuid::uuid4()->toString();

        File::makeDirectory(storage_path($folder));

        $invoiceService = App::make(InvoiceService::class);

        foreach ($this->entries as $key => $id) {
            if ($entry = Invoice::find($id)) {
                $invoiceService
                    ->download(invoice: $entry)
                    ->filename($folder.DIRECTORY_SEPARATOR.$entry->invoice_number)
                    ->save()
                ;
            }
        }

        $zip = new ZipArchive();
        $files = File::files(storage_path('app'.DIRECTORY_SEPARATOR.$folder));
        if ($zip->open(storage_path($folder.'.zip'), \ZipArchive::CREATE)== TRUE) {
            foreach ($files as $file) {
                $relativeName = basename($file);
                $zip->addFile(filepath: $file, entryname: $relativeName);
            }
        }
        $zip->close();

        $invoiceService->sendFileByEmail(filename: $folder.'.zip', email: $this->email);

        File::deleteDirectory(storage_path($folder));
        File::deleteDirectory(storage_path('app'.DIRECTORY_SEPARATOR.$folder));
    }
}
