<?php

namespace App\Filament\Actions;

use App\Models\Enrollment;
use App\Services\CertificateService;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

/**
 * Certificate of an enrollment that has a result. Shown when certificates are enabled in the settings.
 */
class DownloadCertificateAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'downloadCertificate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Certificate'))
            ->icon(Heroicon::OutlinedDocumentCheck)
            ->visible(fn (?Enrollment $record): bool => config('certificates.enabled')
                && $record?->result !== null
                && Gate::allows('view-enrollment', $record))
            ->action(fn (Enrollment $record) => app(CertificateService::class)->certificate($record));
    }
}
