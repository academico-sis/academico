<?php

namespace App\Filament\Resources\Results\Pages;

use App\Filament\Resources\Results\ResultResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Enrollment;
use App\Models\Result;
use App\Models\ResultType;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;

class ViewResult extends ViewRecord
{
    protected static string $resource = ResultResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Result Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('student.name')
                            ->label(__('Student'))
                            ->url(fn (Enrollment $record) => StudentResource::getUrl('edit', ['record' => $record->student_id])),
                        TextEntry::make('course.name')
                            ->label(__('Course')),
                        TextEntry::make('course.period.name')
                            ->label(__('Period')),
                        TextEntry::make('result.result_name.name')
                            ->label(__('Result'))
                            ->badge()
                            ->color(fn (Enrollment $record): ?array => $record->result?->result_name?->color ? Color::hex($record->result->result_name->color) : null)
                            ->placeholder('-'),
                    ]),
                Section::make(__('Grades'))
                    ->schema([
                        TextEntry::make('grades_summary')
                            ->label('')
                            ->state(function (Enrollment $record): string {
                                $grades = $record->grades()->with('gradeType.category')->get();

                                if ($grades->isEmpty()) {
                                    return '-';
                                }

                                return $grades->map(function ($grade) {
                                    $label = $grade->gradeType->complete_name ?? $grade->gradeType->name;

                                    return "{$label}: {$grade->grade}";
                                })->implode(' | ');
                            }),
                    ]),
                Section::make(__('Comments'))
                    ->schema([
                        TextEntry::make('result_comments')
                            ->label('')
                            ->state(fn (Enrollment $record): string => $record->result?->comments?->pluck('body')->implode("\n") ?? '-')
                            ->placeholder('-'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $resultTypes = ResultType::all();

        return $resultTypes->map(fn (ResultType $resultType) => Action::make("set_result_{$resultType->id}")
            ->label($resultType->name)
            ->color(fn () => $resultType->color ? Color::hex($resultType->color) : 'gray')
            ->requiresConfirmation()
            ->action(function () use ($resultType) {
                Result::updateOrCreate(
                    ['enrollment_id' => $this->record->id],
                    ['result_type_id' => $resultType->id],
                );

                $this->record->load('result.result_name');

                Notification::make()
                    ->success()
                    ->title(__('Result updated'))
                    ->send();

                $this->refreshFormData(['result']);
            })
        )->toArray();
    }
}
