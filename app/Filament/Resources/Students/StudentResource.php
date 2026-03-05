<?php

namespace App\Filament\Resources\Students;

use App\Filament\Exports\StudentExporter;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\EnrollStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\RelationManagers\ContactsRelationManager;
use App\Filament\Resources\Students\RelationManagers\EnrollmentsRelationManager;
use App\Models\Period;
use App\Models\Student;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('enrollments.view') ?? false;
    }

    protected static ?int $navigationSort = 200;

    public static function getNavigationGroup(): ?string
    {
        return __('Administration');
    }

    public static function getModelLabel(): string
    {
        return __('Student');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Students');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Student Info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('Name')),
                        TextEntry::make('user.email')
                            ->label(__('Email')),
                        TextEntry::make('idnumber')
                            ->label(__('ID Number'))
                            ->placeholder('-'),
                        TextEntry::make('user.username')
                            ->label(__('Username'))
                            ->placeholder('-'),
                        TextEntry::make('user.birthdate')
                            ->label(__('Birthdate'))
                            ->date(),
                        TextEntry::make('student_age')
                            ->label(__('Age')),
                        TextEntry::make('formatted_gender')
                            ->label(__('Gender'))
                            ->placeholder('-'),
                        TextEntry::make('phone.phone_number')
                            ->label(__('Phone'))
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('profession.name')
                            ->label(__('Profession'))
                            ->placeholder('-'),
                        TextEntry::make('institution.name')
                            ->label(__('Institution'))
                            ->placeholder('-'),
                    ]),
                Section::make(__('Address'))
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('address')
                            ->label(__('Address'))
                            ->placeholder('-'),
                        TextEntry::make('city')
                            ->label(__('City'))
                            ->placeholder('-'),
                        TextEntry::make('zip_code')
                            ->label(__('zip'))
                            ->placeholder('-'),
                        TextEntry::make('state')
                            ->label(__('State'))
                            ->placeholder('-'),
                        TextEntry::make('country')
                            ->label(__('Country'))
                            ->placeholder('-'),
                    ]),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Student')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('Student Info'))
                            ->schema([
                                TextInput::make('firstname')
                                    ->label(__('First name'))
                                    ->required()
                                    ->maxLength(30),
                                TextInput::make('lastname')
                                    ->label(__('Last name'))
                                    ->required()
                                    ->maxLength(30),
                                TextInput::make('email')
                                    ->label(__('Email'))
                                    ->email()
                                    ->nullable()
                                    ->maxLength(60),
                                TextInput::make('idnumber')
                                    ->label(__('ID Number'))
                                    ->nullable(),
                                DatePicker::make('birthdate')
                                    ->label(__('Birthdate'))
                                    ->nullable(),
                                Radio::make('gender_id')
                                    ->label(__('Gender'))
                                    ->options([
                                        0 => __('Other'),
                                        1 => __('Female'),
                                        2 => __('Male'),
                                    ])
                                    ->required()
                                    ->default(0)
                                    ->inline(),
                                SpatieMediaLibraryFileUpload::make('profile_picture')
                                    ->label(__('Profile Picture'))
                                    ->collection('profile-picture')
                                    ->conversion('thumb')
                                    ->image()
                                    ->nullable(),
                                Select::make('profession_id')
                                    ->label(__('Profession'))
                                    ->relationship('profession', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->createOptionForm([
                                        TextInput::make('name')->required(),
                                    ])
                                    ->nullable(),
                                Select::make('institution_id')
                                    ->label(__('Institution'))
                                    ->relationship('institution', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->createOptionForm([
                                        TextInput::make('name')->required(),
                                    ])
                                    ->nullable(),
                                Repeater::make('phone')
                                    ->relationship()
                                    ->label(__('Phone numbers'))
                                    ->schema([
                                        TextInput::make('phone_number')
                                            ->label(__('Phone number'))
                                            ->required(),
                                    ])
                                    ->defaultItems(0)
                                    ->reorderable(false),
                            ]),

                        Tab::make(__('Address'))
                            ->schema([
                                TextInput::make('address')
                                    ->label(__('Address'))
                                    ->nullable()
                                    ->maxLength(60),
                                TextInput::make('zip_code')
                                    ->label(__('zip'))
                                    ->nullable()
                                    ->maxLength(10),
                                TextInput::make('city')
                                    ->label(__('City'))
                                    ->nullable()
                                    ->maxLength(30),
                                TextInput::make('state')
                                    ->label(__('State'))
                                    ->nullable()
                                    ->maxLength(30),
                                TextInput::make('country')
                                    ->label(__('Country'))
                                    ->nullable()
                                    ->maxLength(20),
                            ]),

                        Tab::make(__('Invoicing Info'))
                            ->schema([
                                TextInput::make('iban')
                                    ->label(__('IBAN'))
                                    ->nullable()
                                    ->maxLength(90),
                                TextInput::make('bic')
                                    ->label(__('BIC'))
                                    ->nullable()
                                    ->maxLength(30),
                            ])
                            ->visible(fn (): bool => (bool) config('settings.collect_student_banking_info')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Mobile: stacked student info (name + email · phone)
                TextColumn::make('mobile_name')
                    ->label(__('Student'))
                    ->state(fn ($record) => $record->user?->lastname.', '.$record->user?->firstname)
                    ->description(fn ($record) => collect([$record->user?->email, $record->phone->first()?->phone_number])->filter()->implode(' · '))
                    ->searchable(query: fn ($query, $search) => $query->whereHas('user', fn ($q) => $q->where('lastname', 'like', "%{$search}%")->orWhere('firstname', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
                    ->sortable(query: fn ($query, $direction) => $query->join('users', 'students.user_id', '=', 'users.id')->orderBy('users.lastname', $direction))
                    ->wrap()
                    ->hiddenFrom('md'),
                // Desktop columns
                TextColumn::make('idnumber')
                    ->label(__('ID'))
                    ->searchable()
                    ->visibleFrom('md'),
                TextColumn::make('user.lastname')
                    ->label(__('Last name'))
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('user.firstname')
                    ->label(__('First name'))
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('user.email')
                    ->label(__('Email'))
                    ->wrap()
                    ->width('180px')
                    ->searchable()
                    ->visibleFrom('md'),
                TextColumn::make('user.username')
                    ->label(__('Username'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('student_age')
                    ->label(__('Age'))
                    ->visibleFrom('md'),
                TextColumn::make('user.birthdate')
                    ->label(__('Birthdate'))
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->visibleFrom('lg'),
                TextColumn::make('phone.phone_number')
                    ->label(__('Phone'))
                    ->badge()
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make('institution_id')
                    ->relationship('institution', 'name')
                    ->label(__('Institution'))
                    ->preload()
                    ->searchable(),
                Filter::make('age')
                    ->label(__('Age'))
                    ->schema([
                        TextInput::make('min_age')
                            ->label(__('Min Age'))
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('max_age')
                            ->label(__('Max Age'))
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->columns(2)
                    ->query(function ($query, array $data) {
                        if ($data['min_age']) {
                            $query->whereHas('user', fn ($q) => $q
                                ->where('birthdate', '<=', Carbon::now()->subYears((int) $data['min_age'])));
                        }
                        if ($data['max_age']) {
                            $query->whereHas('user', fn ($q) => $q
                                ->where('birthdate', '>=', Carbon::now()->subYears((int) $data['max_age'] + 1)));
                        }
                    }),
                TernaryFilter::make('new_in_period')
                    ->label(__('New Students'))
                    ->queries(
                        true: function ($query) {
                            $period = Period::get_default_period();
                            if ($period) {
                                $query->newInPeriod($period->id);
                            }
                        },
                        false: function ($query) {
                            $period = Period::get_default_period();
                            if ($period) {
                                $newIds = Period::find($period->id)->newStudents()->pluck('student_id')->toArray();
                                $query->whereNotIn('id', $newIds);
                            }
                        },
                    ),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(StudentExporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(StudentExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ContactsRelationManager::class,
            EnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
            'enroll' => EnrollStudent::route('/{record}/enroll'),
        ];
    }
}
