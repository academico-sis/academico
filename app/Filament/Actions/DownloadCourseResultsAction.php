<?php

namespace App\Filament\Actions;

use App\Models\Course;
use App\Services\CertificateService;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

/**
 * Grades and results of every student in a course.
 */
class DownloadCourseResultsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'downloadCourseResults';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Course results'))
            ->icon(Heroicon::OutlinedTableCells)
            ->visible(fn (?Course $record): bool => $record !== null && Gate::allows('view-course-grades', $record))
            ->action(fn (Course $record) => app(CertificateService::class)->courseResults($record));
    }
}
