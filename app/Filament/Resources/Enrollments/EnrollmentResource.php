<?php

namespace App\Filament\Resources\Enrollments;

use App\Filament\Exports\EnrollmentExporter;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Enrollments\Pages\ChangeEnrollmentCourse;
use App\Filament\Resources\Enrollments\Pages\ListEnrollments;
use App\Filament\Resources\Enrollments\Pages\ViewEnrollment;
use App\Filament\Resources\Enrollments\RelationManagers\EnrollmentCommentsRelationManager;
use App\Filament\Resources\Enrollments\RelationManagers\ScholarshipsRelationManager;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Enrollment;
use App\Models\Period;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
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

    public static function canAccess(): bool
    {
        return auth()->user()?->can('enrollments.view') ?? false;
    }

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
                            ->color(fn (Enrollment $record): ?array => $record->enrollmentStatus?->color ? Color::hex($record->enrollmentStatus->color) : null),
                        TextEntry::make('total_price')
                            ->label(__('Price'))
                            ->money(config('academico.currency_code', 'USD')),
                        TextEntry::make('childrenEnrollments.course.name')
                            ->label(__('Children Enrollments'))
                            ->placeholder('-'),
                        TextEntry::make('course.teacher.name')
                            ->label(__('Teacher'))
                            ->placeholder('-'),
                        TextEntry::make('result.result_name.name')
                            ->label(__('Result'))
                            ->placeholder('-'),
                        TextEntry::make('attendance_ratio')
                            ->label(__('Attendance'))
                            ->suffix('%')
                            ->placeholder('-'),
                    ]),
                Section::make(__('Books'))
                    ->schema([
                        TextEntry::make('course.books.name')
                            ->label(__('Books'))
                            ->badge()
                            ->placeholder(__('No books assigned')),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $defaultPeriod = Period::get_default_period();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('student.user.lastname')
                    ->label(__('Last name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Enrollment $record) => $record->student?->user?->firstname)
                    ->visibleFrom('md'),
                // Mobile: stacked student + course info
                TextColumn::make('mobile_student')
                    ->label(__('Student'))
                    ->state(fn (Enrollment $record) => $record->student?->user?->lastname.' '.$record->student?->user?->firstname)
                    ->weight('bold')
                    ->description(fn (Enrollment $record) => $record->course?->name)
                    ->searchable(query: fn ($query, $search) => $query->whereHas('student.user', fn ($q) => $q->where('lastname', 'like', "%{$search}%")->orWhere('firstname', 'like', "%{$search}%")))
                    ->wrap()
                    ->hiddenFrom('md'),
                TextColumn::make('course.name')
                    ->label(__('Course'))
                    ->wrap()
                    ->width('200px')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('course.period.name')
                    ->label(__('Period'))
                    ->sortable()
                    ->visibleFrom('lg'),
                TextColumn::make('enrollmentStatus.name')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (Enrollment $record): ?array => $record->enrollmentStatus?->color ? Color::hex($record->enrollmentStatus->color) : null),
                TextColumn::make('total_price')
                    ->label(__('Price'))
                    ->money(config('academico.currency_code', 'USD'))
                    ->sortable(),
                TextColumn::make('student.user.email')
                    ->label(__('Email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visibleFrom('md'),
                TextColumn::make('scholarships.name')
                    ->label(__('Scholarships'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.idnumber')
                    ->label(__('ID Number'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.student_age')
                    ->label(__('Age'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.formatted_gender')
                    ->label(__('Gender'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.birthdate')
                    ->label(__('Birthdate'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student.phone.phone_number')
                    ->label(__('Phone'))
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
            ->headerActions([
                ExportAction::make()
                    ->exporter(EnrollmentExporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(EnrollmentExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ScholarshipsRelationManager::class,
            EnrollmentCommentsRelationManager::class,
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
