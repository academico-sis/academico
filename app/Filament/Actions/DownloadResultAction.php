<?php

namespace App\Filament\Actions;

use App\Models\Enrollment;
use App\Services\CertificateService;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

/**
 * Result sheet of an enrollment: grades, skills, result and comments.
 */
class DownloadResultAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'downloadResult';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Result sheet'))
            ->icon(Heroicon::OutlinedDocumentText)
            ->visible(fn (?Enrollment $record): bool => $record !== null
                && ($record->result !== null || $record->course?->evaluationType !== null)
                && Gate::allows('view-enrollment', $record))
            ->action(fn (Enrollment $record) => app(CertificateService::class)->result($record));
    }
}
