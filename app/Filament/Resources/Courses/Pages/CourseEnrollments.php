<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Pages\StudentAttendance;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Enrollments\EnrollmentResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;

class CourseEnrollments extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = CourseResource::class;

    protected string $view = 'filament.resources.courses.pages.course-enrollments';

    #[Url]
    public string $viewMode = 'list';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public static function canAccess(array $parameters = []): bool
    {
        if (! isset($parameters['record'])) {
            return false;
        }

        $course = $parameters['record'] instanceof Course
            ? $parameters['record']
            : Course::find($parameters['record']);

        if (! $course) {
            return false;
        }

        return Gate::allows('view-course', $course);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->getRecord()->enrollments()->getQuery())
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('student.user.lastname')
                    ->label(__('Last name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.user.firstname')
                    ->label(__('First name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('enrollmentStatus.name')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn ($record): ?array => $record->enrollmentStatus?->color ? Color::hex($record->enrollmentStatus->color) : null),
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
            ->defaultSort('id', 'desc')
            ->recordUrl(fn ($record) => auth()->user()->can('enrollments.edit')
                ? EnrollmentResource::getUrl('view', ['record' => $record])
                : StudentResource::getUrl('edit', ['record' => $record->student_id]))
            ->recordActions([
                Action::make('view_student')
                    ->label(__('Student'))
                    ->icon('heroicon-o-user')
                    ->url(fn ($record) => StudentResource::getUrl('edit', ['record' => $record->student_id]))
                    ->visible(fn () => auth()->user()->can('enrollments.edit')),
                Action::make('view_attendance')
                    ->label(__('Attendance'))
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn ($record) => StudentAttendance::getUrl(['studentId' => $record->student_id, 'courseId' => $record->course_id])),
            ]);
    }

    public function getTitle(): string
    {
        return __('Enrollments').' — '.$this->getRecord()->name;
    }

    public function getCourseInfo(): array
    {
        $course = $this->getRecord();

        return [
            'teacher' => $course->teacher?->name ?? '-',
            'schedule' => $course->course_times ?: '-',
            'room' => $course->room?->name ?? '-',
            'period' => $course->period?->name ?? '-',
            'level' => $course->level?->name ?? '-',
            'rhythm' => $course->rhythm?->name ?? '-',
            'enrollments' => $course->course_enrollments_count ?? 0,
        ];
    }

    public function getRosterEnrollments(): \Illuminate\Support\Collection
    {
        return $this->getRecord()
            ->enrollments()
            ->with('student.media', 'student.user')
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enroll_student')
                ->label(__('Enroll a student'))
                ->icon('heroicon-o-plus-circle')
                ->form([
                    Select::make('student_id')
                        ->label(__('Student'))
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search) => Student::query()
                            ->whereHas('user', fn (Builder $query) => $query
                                ->where('firstname', 'like', "%{$search}%")
                                ->orWhere('lastname', 'like', "%{$search}%"))
                            ->limit(50)
                            ->get()
                            ->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn ($value) => Student::find($value)?->name)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $course = $this->getRecord();

                    Enrollment::create([
                        'student_id' => $data['student_id'],
                        'course_id' => $course->id,
                        'status_id' => 1,
                        'total_price' => $course->price,
                    ]);

                    Notification::make()
                        ->title(__('Student enrolled successfully'))
                        ->success()
                        ->send();
                })
                ->visible(fn () => Gate::allows('enroll-in-course', $this->getRecord())),
            Action::make('switch_to_roster')
                ->label(__('Photo roster'))
                ->icon('heroicon-o-photo')
                ->action(fn () => $this->viewMode = 'roster')
                ->visible(fn () => $this->viewMode === 'list'),
            Action::make('switch_to_list')
                ->label(__('List view'))
                ->icon('heroicon-o-list-bullet')
                ->action(fn () => $this->viewMode = 'list')
                ->visible(fn () => $this->viewMode === 'roster'),
        ];
    }
}
