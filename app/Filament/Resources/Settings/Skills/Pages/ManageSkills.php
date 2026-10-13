<?php

namespace App\Filament\Resources\Settings\Skills\Pages;

use App\Filament\Resources\Settings\Skills\SkillResource;
use App\Models\EvaluationType;
use App\Models\Level;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillType;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ManageSkills extends ManageRecords
{
    protected static string $resource = SkillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('importCsv')
                ->label(__('Import CSV'))
                ->icon('heroicon-o-arrow-up-tray')
                ->schema([
                    FileUpload::make('file')
                        ->label(__('CSV File'))
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel'])
                        ->required(),
                    TextInput::make('group')
                        ->label(__('Evaluation Type Group Name'))
                        ->helperText(__('Optionally group imported skills into a new evaluation type')),
                ])
                ->action(function (array $data): void {
                    $filePath = storage_path('app/public/'.$data['file']);

                    try {
                        $csv = Reader::from($filePath, 'r');
                    } catch (\Exception $e) {
                        Log::error('[ManageSkills::importCsv] Failed to read CSV file', [
                            'exception' => $e::class,
                            'message' => $e->getMessage(),
                            'file_path' => $filePath,
                        ]);

                        if (function_exists('\Sentry\captureException')) {
                            \Sentry\captureException($e);
                        }

                        Notification::make()->danger()->title(__('Invalid file.'))->send();

                        return;
                    }

                    $csv->setDelimiter(',');
                    $csv->setHeaderOffset(0);

                    // Validate all rows first
                    foreach ($csv as $record) {
                        $values = array_values($record);
                        if (! ($values[2] ?? null) || ! Level::firstWhere(['name' => $values[2]])) {
                            Notification::make()->danger()->title(__('The file contains invalid levels.'))->send();

                            return;
                        }
                        if (! ($values[1] ?? null)) {
                            Notification::make()->danger()->title(__('The file contains invalid skills.'))->send();

                            return;
                        }
                        if (! ($values[0] ?? null) || ! (SkillType::firstWhere(['shortname' => $values[0]]) ?? SkillType::firstWhere(['name' => $values[0]]))) {
                            Notification::make()->danger()->title(__('The file contains invalid skill type categories.'))->send();

                            return;
                        }
                    }

                    $group = null;
                    if ($data['group'] ?? null) {
                        if (EvaluationType::firstWhere(['name' => $data['group']])) {
                            Notification::make()->danger()->title(__('This name is already used for another evaluation type.'))->send();

                            return;
                        }
                        $group = EvaluationType::create(['name' => $data['group']]);
                    }

                    try {
                        $count = 0;
                        foreach ($csv as $record) {
                            $values = array_values($record);
                            $skillType = SkillType::firstWhere(['shortname' => $values[0]]) ?? SkillType::firstWhere(['name' => $values[0]]);
                            $level = Level::firstWhere(['name' => $values[2]]);

                            $skill = Skill::create([
                                'skill_type_id' => $skillType->id,
                                'name' => $values[1],
                                'level_id' => $level->id,
                            ]);

                            if ($group) {
                                $group->skills()->save($skill);
                            }

                            $count++;
                        }

                        @unlink($filePath);

                        Notification::make()->success()->title(__(':count skills imported.', ['count' => $count]))->send();
                    } catch (\Throwable $e) {
                        Log::error('[ManageSkills::importCsv] Failed during skill import', [
                            'exception' => $e::class,
                            'message' => $e->getMessage(),
                        ]);

                        if (function_exists('\Sentry\captureException')) {
                            \Sentry\captureException($e);
                        }

                        @unlink($filePath);

                        Notification::make()->danger()->title(__('An error occurred during import.'))->send();
                    }
                }),
        ];
    }
}
