<?php

namespace App\Filament\Resources\Results\Pages;

use App\Filament\Resources\Results\ResultResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Enrollment;
use App\Models\Result;
use App\Models\ResultType;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
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
        return [
            Action::make('edit_result')
                ->label(__('Edit Result'))
                ->icon('heroicon-o-pencil-square')
                ->fillForm(fn () => [
                    'result_type_id' => $this->record->result?->result_type_id,
                ])
                ->form([
                    Select::make('result_type_id')
                        ->label(__('Result'))
                        ->options(ResultType::all()->pluck('name', 'id'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    Result::updateOrCreate(
                        ['enrollment_id' => $this->record->id],
                        ['result_type_id' => $data['result_type_id']],
                    );

                    Notification::make()
                        ->success()
                        ->title(__('Result updated'))
                        ->send();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),
        ];
    }
}
