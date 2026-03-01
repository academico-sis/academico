<?php

namespace App\Filament\Resources\Enrollments;

use App\Filament\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Enrollments\Pages\ChangeEnrollmentCourse;
use App\Filament\Resources\Enrollments\Pages\ListEnrollments;
use App\Filament\Resources\Enrollments\Pages\ViewEnrollment;
use App\Filament\Resources\Enrollments\RelationManagers\ScholarshipsRelationManager;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Enrollment;
use App\Models\Period;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 210;

    public static function getNavigationGroup(): ?string
    {
        return __('Administration');
    }

    public static function getModelLabel(): string
    {
        return __('Enrollment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Enrollments');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Enrollment Info'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('student.name')
                            ->label(__('Student'))
                            ->url(fn (Enrollment $record) => StudentResource::getUrl('edit', ['record' => $record->student_id])),
                        TextEntry::make('created_at')
                            ->label(__('Enrollment date'))
                            ->date(),
                        TextEntry::make('id')
                            ->label(__('Enrollment ID')),
                        TextEntry::make('course.name')
                            ->label(__('Course'))
                            ->url(fn (Enrollment $record) => CourseResource::getUrl('edit', ['record' => $record->course_id])),
                        TextEntry::make('course.period.name')
                            ->label(__('Period')),
                        TextEntry::make('enrollmentStatus.name')
                            ->label(__('Status'))
                            ->badge()
                            ->color(fn (Enrollment $record): ?string => $record->enrollmentStatus?->color),
                        TextEntry::make('total_price')
                            ->label(__('Price'))
                            ->money(config('academico.currency_code', 'USD')),
                        TextEntry::make('childrenEnrollments.course.name')
                            ->label(__('Children Enrollments'))
                            ->placeholder('-'),
                        TextEntry::make('result.result_name.name')
                            ->label(__('Result'))
                            ->placeholder('-'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $defaultPeriod = Period::get_default_period();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                TextColumn::make('student.user.lastname')
                    ->label(__('Last name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.user.firstname')
                    ->label(__('First name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.name')
                    ->label(__('Course'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.period.name')
                    ->label(__('Period'))
                    ->sortable(),
                TextColumn::make('enrollmentStatus.name')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (Enrollment $record): ?string => $record->enrollmentStatus?->color),
                TextColumn::make('total_price')
                    ->label(__('Price'))
                    ->money(config('academico.currency_code', 'USD'))
                    ->sortable(),
                TextColumn::make('student.user.email')
                    ->label(__('Email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('scholarships.name')
                    ->label(__('Scholarships'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->query(function ($query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('course', fn ($q) => $q->where('period_id', $data['value']));
                        }
                    })
                    ->options(fn () => Period::pluck('name', 'id'))
                    ->default($defaultPeriod?->id)
                    ->label(__('Period')),
                SelectFilter::make('status_id')
                    ->relationship('enrollmentStatus', 'name')
                    ->label(__('Status'))
                    ->multiple()
                    ->preload(),
                SelectFilter::make('scholarship')
                    ->relationship('scholarships', 'name')
                    ->label(__('Scholarship'))
                    ->preload(),
                Filter::make('age')
                    ->label(__('Age'))
                    ->schema([
                        TextInput::make('age')
                            ->label(__('Age'))
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['age']) {
                            $age = (int) $data['age'];
                            $query->whereHas('student', fn ($q) => $q
                                ->where('birthdate', '<=', Carbon::now()->subYears($age))
                                ->where('birthdate', '>', Carbon::now()->subYears($age + 1)));
                        }
                    }),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->defaultSort('id', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ScholarshipsRelationManager::class,
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEnrollments::route('/'),
            'view' => ViewEnrollment::route('/{record}'),
            'change-course' => ChangeEnrollmentCourse::route('/{record}/change-course'),
        ];
    }
}
