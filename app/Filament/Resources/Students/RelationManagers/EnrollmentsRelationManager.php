<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'course.events',
                'course.teacher',
                'course.period',
                'student.attendance',
                'result.result_name',
                'enrollmentStatus',
            ]))
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                TextColumn::make('course.name')
                    ->label(__('Course'))
                    ->searchable(),
                TextColumn::make('course.period.name')
                    ->label(__('Period')),
                TextColumn::make('course.teacher.name')
                    ->label(__('Teacher')),
                TextColumn::make('enrollmentStatus.name')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn ($record): ?array => $record->enrollmentStatus?->color ? Color::hex($record->enrollmentStatus->color) : null),
                TextColumn::make('result.result_name.name')
                    ->label(__('Result'))
                    ->placeholder(__('N/A')),
                TextColumn::make('attendance_ratio')
                    ->label(__('Attendance'))
                    ->state(fn ($record): string => $record->attendance_ratio !== null ? $record->attendance_ratio.'%' : __('N/A'))
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        $record->attendance_ratio === null => 'gray',
                        $record->attendance_ratio >= 80 => 'success',
                        $record->attendance_ratio >= 50 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('total_price')
                    ->label(__('Price'))
                    ->money(config('academico.currency_code', 'USD'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.student_age')
                    ->label(__('Age'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.user.birthdate')
                    ->label(__('Birthdate'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.phone.phone_number')
                    ->label(__('Phone'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('Enrolled'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultGroup(
                Group::make('course.period.name')
                    ->label(__('Period'))
                    ->collapsible()
            )
            ->recordUrl(fn ($record) => EnrollmentResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                Action::make('enroll')
                    ->label(__('Enroll in a course'))
                    ->icon('heroicon-o-plus-circle')
                    ->url(fn () => StudentResource::getUrl('enroll', ['record' => $this->getOwnerRecord()])),
            ]);
    }
}
