<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EnrollStudent extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = StudentResource::class;

    protected string $view = 'filament.resources.students.pages.enroll-student';

    public static function canAccess(array $parameters = []): bool
    {
        return Gate::allows('enroll-students');
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
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
                Action::make('enroll')
                    ->label(__('Enroll'))
                    ->icon('heroicon-o-plus-circle')
                    ->requiresConfirmation()
                    ->action(function (Course $record) {
                        Enrollment::create([
                            'student_id' => $this->getRecord()->id,
                            'course_id' => $record->id,
                            'status_id' => 1,
                            'total_price' => $record->price,
                        ]);

                        Notification::make()
                            ->title(__('Student enrolled successfully'))
                            ->success()
                            ->send();

                        $this->redirect(StudentResource::getUrl('edit', ['record' => $this->getRecord()]));
                    }),
            ]);
    }

    public function getTitle(): string
    {
        return __('Enroll Student').' — '.$this->getRecord()->name;
    }
}
