<?php

namespace App\Filament\Resources\Enrollments\Pages;

use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Models\Course;
use App\Models\Period;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ChangeEnrollmentCourse extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = EnrollmentResource::class;

    protected string $view = 'filament.resources.enrollments.pages.change-enrollment-course';

    /** @var array<int, ?string> refusal reasons by course ID, computed once per request */
    protected array $enrollmentRefusalReasons = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canEdit($this->record), 403);
    }

    public function table(Table $table): Table
    {
        $defaultPeriod = Period::get_default_period();

        return $table
            ->query(fn () => Course::query())
            ->columns([
                TextColumn::make('rhythm.name')
                    ->label(__('Rhythm'))
                    ->sortable(),
                TextColumn::make('level.name')
                    ->label(__('Level'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('volume')
                    ->label(__('Volume'))
                    ->suffix('h')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('teacher.name')
                    ->label(__('Teacher'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('room.name')
                    ->label(__('Room'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('course_times')
                    ->label(__('Schedule'))
                    ->toggleable(),
                TextColumn::make('course_enrollments_count')
                    ->label(__('Enrollments'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('start_date')
                    ->label(__('Start Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('end_date')
                    ->label(__('End Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('period_id')
                    ->relationship('period', 'name')
                    ->label(__('Period'))
                    ->default($defaultPeriod?->id)
                    ->preload(),
                SelectFilter::make('rhythm_id')
                    ->relationship('rhythm', 'name')
                    ->label(__('Rhythm'))
                    ->preload(),
                SelectFilter::make('teacher_id')
                    ->relationship('teacher', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->label(__('Teacher'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('level_id')
                    ->relationship('level', 'name')
                    ->label(__('Level'))
                    ->preload(),
            ])
            ->defaultSort('start_date', 'desc')
            ->recordActions([
                Action::make('select')
                    ->label(__('Select'))
                    ->icon('heroicon-o-check-circle')
                    ->disabled(fn (Course $record): bool => filled($this->getEnrollmentRefusalReason($record)))
                    ->tooltip(fn (Course $record): ?string => $this->getEnrollmentRefusalReason($record))
                    ->requiresConfirmation()
                    ->action(function (Course $record) {
                        $refusalReason = $this->getEnrollmentRefusalReason($record);

                        if ($refusalReason) {
                            Notification::make()
                                ->title($refusalReason)
                                ->danger()
                                ->send();

                            return;
                        }

                        $this->getRecord()->update([
                            'course_id' => $record->id,
                            'total_price' => $record->price,
                        ]);

                        Notification::make()
                            ->title(__('The enrollment has been updated'))
                            ->success()
                            ->send();

                        $this->redirect(EnrollmentResource::getUrl('view', ['record' => $this->getRecord()]));
                    }),
            ]);
    }

    protected function getEnrollmentRefusalReason(Course $course): ?string
    {
        if (! array_key_exists($course->id, $this->enrollmentRefusalReasons)) {
            $this->enrollmentRefusalReasons[$course->id] = $this->getRecord()->student->getEnrollmentRefusalReason($course);
        }

        return $this->enrollmentRefusalReasons[$course->id];
    }

    public function getTitle(): string
    {
        return __('Change course').' — '.$this->getRecord()->name;
    }
}
