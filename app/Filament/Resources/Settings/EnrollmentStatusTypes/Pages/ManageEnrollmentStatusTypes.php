<?php

namespace App\Filament\Resources\Settings\EnrollmentStatusTypes\Pages;

use App\Filament\Resources\Settings\EnrollmentStatusTypes\EnrollmentStatusTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEnrollmentStatusTypes extends ManageRecords
{
    protected static string $resource = EnrollmentStatusTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
