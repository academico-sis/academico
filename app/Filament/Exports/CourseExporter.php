<?php

namespace App\Filament\Exports;

use App\Models\Course;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CourseExporter extends Exporter
{
    protected static ?string $model = Course::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('rhythm.name')
                ->label(__('Rhythm')),
            ExportColumn::make('level.name')
                ->label(__('Level')),
            ExportColumn::make('name')
                ->label(__('Name')),
            ExportColumn::make('volume')
                ->label(__('Volume')),
            ExportColumn::make('remote_volume')
                ->label(__('Remote volume')),
            ExportColumn::make('teacher.name')
                ->label(__('Teacher')),
            ExportColumn::make('room.name')
                ->label(__('Room')),
            ExportColumn::make('course_enrollments_count')
                ->label(__('Enrollments')),
            ExportColumn::make('start_date')
                ->label(__('Start Date')),
            ExportColumn::make('end_date')
                ->label(__('End Date')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('Your course export has completed and :count rows exported.', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__(':count rows failed to export.', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
