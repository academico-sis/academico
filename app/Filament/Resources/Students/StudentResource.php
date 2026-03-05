<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\EnrollStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\RelationManagers\ContactsRelationManager;
use App\Filament\Resources\Students\RelationManagers\EnrollmentsRelationManager;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function canAccess(): bool
    {
        return ! (auth()->user()?->isTeacher() ?? false);
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
                TextColumn::make('idnumber')
                    ->label(__('ID'))
                    ->searchable(),
                TextColumn::make('user.lastname')
                    ->label(__('Last name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.firstname')
                    ->label(__('First name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label(__('Email'))
                    ->searchable(),
                TextColumn::make('student_age')
                    ->label(__('Age')),
                TextColumn::make('user.birthdate')
                    ->label(__('Birthdate'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('phone.phone_number')
                    ->label(__('Phone'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('institution_id')
                    ->relationship('institution', 'name')
                    ->label(__('Institution'))
                    ->preload()
                    ->searchable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                EditAction::make(),
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
            ContactsRelationManager::class,
            EnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
            'enroll' => EnrollStudent::route('/{record}/enroll'),
        ];
    }
}
