<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Event;
use App\Services\CourseEventScheduler;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->authorizationTooltip(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['apply_changes_from'] = today()->toDateString();

        return $data;
    }

    protected function beforeSave(): void
    {
        $date = Carbon::parse($this->data['apply_changes_from'], config('settings.courses_timezone'));

        // "from today" means from now on: the sessions that already took place today are kept as they are
        app(CourseEventScheduler::class)->applyChangesFrom($date->isToday() ? Carbon::now(config('settings.courses_timezone')) : $date->startOfDay());
    }

    protected function afterSave(): void
    {
        app(CourseEventScheduler::class)->applyChangesFrom(null);

        $sessionsOutsideCourseDates = Event::where('course_id', $this->record->id)
            ->where(fn ($query) => $query
                ->whereDate('start', '<', $this->record->start_date)
                ->orWhereDate('start', '>', $this->record->end_date))
            ->count();

        if ($sessionsOutsideCourseDates > 0) {
            Notification::make()
                ->title(trans_choice(':count session is outside the course dates|:count sessions are outside the course dates', $sessionsOutsideCourseDates))
                ->body(__('They were kept because they have recorded attendance or are before the date from which the changes apply. Delete them from the events list if they did not take place.'))
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
