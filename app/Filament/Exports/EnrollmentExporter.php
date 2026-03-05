<?php

namespace App\Filament\Exports;

use App\Models\Enrollment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EnrollmentExporter extends Exporter
{
    protected static ?string $model = Enrollment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label(__('ID')),
            ExportColumn::make('student.user.lastname')
                ->label(__('Last name')),
            ExportColumn::make('student.user.firstname')
                ->label(__('First name')),
            ExportColumn::make('course.name')
                ->label(__('Course')),
            ExportColumn::make('course.period.name')
                ->label(__('Period')),
            ExportColumn::make('enrollmentStatus.name')
                ->label(__('Status')),
            ExportColumn::make('total_price')
                ->label(__('Price')),
            ExportColumn::make('student.user.email')
                ->label(__('Email')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('Your enrollment export has completed and :count rows exported.', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__(':count rows failed to export.', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
