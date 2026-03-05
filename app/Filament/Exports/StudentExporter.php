<?php

namespace App\Filament\Exports;

use App\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('idnumber')
                ->label(__('ID')),
            ExportColumn::make('user.lastname')
                ->label(__('Last name')),
            ExportColumn::make('user.firstname')
                ->label(__('First name')),
            ExportColumn::make('user.email')
                ->label(__('Email')),
            ExportColumn::make('student_age')
                ->label(__('Age')),
            ExportColumn::make('user.birthdate')
                ->label(__('Birthdate')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('Your student export has completed and :count rows exported.', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__(':count rows failed to export.', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
}
