<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Partner;
use App\Models\Period;
use App\Models\Year;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DateRange
{
    public function __construct(
        public DateTime $start,
        public DateTime $end,
    ) {
    }
}

class StatService
{
    public Builder $coursesQuery;

    public function __construct(
        private bool $external,
        private Period|Year|DateRange $reference,
        private ?Partner $partner = null,
    ) {
        $this->coursesQuery = match ($this->reference::class) {
            Period::class => $this->applyQueryScopes(Course::where('period_id', $this->reference->id)),
            Year::class => $this->applyQueryScopes(Course::whereIn('period_id', $this->reference->periods->pluck('id'))),
            DateRange::class => $this->applyQueryScopes(Course::where('end_date', '>', $this->reference->start)->where('start_date', '<', $this->reference->end)),
            default => abort(422, 'Stats requested for undefined reference period'),
        };
    }

    public function coursesCount(): int
    {
        return $this->coursesQuery->count();
    }

    public function partnershipsCount(): int
    {
        return $this->coursesQuery->pluck('partner_id')->unique()->count();
    }

    public function enrollmentsCount(): int
    {
        if ($this->external) {
            return $this->coursesQuery->sum('head_count');
        }

        return $this->paidEnrollmentsCount() + $this->pendingEnrollmentsCount();
    }

    public function studentsCount(?int $gender = null): int
    {
        if ($this->external) {
            return $this->coursesQuery->sum('new_students');
        }

        return match ($this->reference::class) {
            Year::class => $this->countInternalStudentsForYear($gender),
            Period::class => $this->countInternalStudentsForPeriod($gender),
            DateRange::class => throw new InvalidArgumentException('Logic error'),
        };
    }

    public function taughtHoursCount(): int
    {
        return $this->coursesQuery->whereNull('parent_course_id')->get()->sum('total_volume');
    }

    public function soldHoursCount(): int
    {
        $total = 0;

        if ($this->external) {
            foreach ($this->coursesQuery->get() as $course) {
                $total += $course->total_volume * $course->head_count;
            }

            return $total;
        }

        foreach ($this->coursesQuery->withCount('real_enrollments')->get() as $course) {
            $total += $course->total_volume * $course->real_enrollments_count;
        }

        return $total;
    }

    public function pendingEnrollmentsCount(): int
    {
        return match ($this->reference::class) {
            Period::class => $this->getPendingEnrollmentsCountForPeriod($this->reference),
            Year::class => $this->getPendingEnrollmentsCountForYear($this->reference),
            default => throw new InvalidArgumentException('Logic error'),
        };
    }

    public function paidEnrollmentsCount(): int
    {
        return match ($this->reference::class) {
            Period::class => $this->getPaidEnrollmentsCountForPeriod($this->reference),
            Year::class => $this->getPaidEnrollmentsCountForYear($this->reference),
            default => throw new InvalidArgumentException('Logic error'),
        };
    }

    private function countInternalStudentsForYear(?int $gender = null)
    {
        if ($this->reference::class !== Year::class) {
            abort(422, 'Logic error');
        }

        if (in_array($gender, Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)) {
            return DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', 'courses.id')
                ->join('periods', 'courses.period_id', 'periods.id')
                ->join('students', 'enrollments.student_id', 'students.id')
                ->where('periods.year_id', $this->reference->id)
                ->whereIn('enrollments.status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)
                ->where('enrollments.parent_id', null)->where('enrollments.deleted_at', null)
                ->where('students.gender_id', $gender)
                ->distinct('student_id')->count('enrollments.student_id');
        }

        if ($gender === 0) {
            return DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', 'courses.id')
                ->join('periods', 'courses.period_id', 'periods.id')
                ->join('students', 'enrollments.student_id', 'students.id')
                ->where('periods.year_id', $this->reference->id)
                ->whereIn('enrollments.status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)
                ->where('enrollments.parent_id', null)->where('enrollments.deleted_at', null)
                ->where(function ($query) {
                    return $query->where('students.gender_id', 0)->orWhereNull('students.gender_id');
                })
                ->distinct('student_id')->count('enrollments.student_id');
        }

        return DB::table('enrollments')->join('courses', 'enrollments.course_id', 'courses.id')->join('periods', 'courses.period_id', 'periods.id')->where('periods.year_id', $this->reference->id)->whereIn('enrollments.status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)
        ->where('enrollments.parent_id', null)->where('enrollments.deleted_at', null)->distinct('student_id')->count('enrollments.student_id');
    }

    private function countInternalStudentsForPeriod(?int $gender = null)
    {
        if ($this->reference::class !== Period::class) {
            abort(422, 'Logic error');
        }

        if (in_array($gender, Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)) {
            return DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', 'courses.id')
                ->join('students', 'enrollments.student_id', 'students.id')
                ->where('courses.period_id', $this->reference->id)
                ->where('enrollments.deleted_at', null)
                ->where('enrollments.parent_id', null)
                ->where('students.gender_id', $gender)
                ->whereIn('enrollments.status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)
                ->distinct('student_id')
                ->count('enrollments.student_id');
        }

        if ($gender === 0) {
            return DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', 'courses.id')
                ->join('students', 'enrollments.student_id', 'students.id')
                ->where('courses.period_id', $this->reference->id)
                ->where('enrollments.deleted_at', null)
                ->where('enrollments.parent_id', null)
                ->where(function ($query) {
                    return $query->where('students.gender_id', 0)->orWhereNull('students.gender_id');
                })
                ->whereIn('enrollments.status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)
                ->distinct('student_id')
                ->count('enrollments.student_id');
        }

        return DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', 'courses.id')
            ->where('courses.period_id', $this->reference->id)
            ->where('enrollments.deleted_at', null)
            ->where('enrollments.parent_id', null)
            ->whereIn('enrollments.status_id', Enrollment::ENROLLMENT_STATUSES_TO_COUNT_IN_STATS)
            ->distinct('student_id')
            ->count('enrollments.student_id');
    }

    /**
     * QUERY BUILDERS
     */
    private function applyQueryScopes(Builder $query): Builder
    {
        if ($this->partner !== null) {
            $query->where('partner_id', $this->partner->id);
        } elseif ($this->external) {
            $query->external();
        } else {
            $query->internal();
        }

        return $query;
    }

    private function getPendingEnrollmentsCountForPeriod(Period $period): int
    {
        return $period->enrollments->where('status_id', 1)->where('parent_id', null)->count();
    }

    private function getPendingEnrollmentsCountForYear(Year $year)
    {
        $total = 0;

        foreach ($year->periods as $period) {
            $total += $this->getPendingEnrollmentsCountForPeriod($period);
        }

        return $total;
    }

    private function getPaidEnrollmentsCountForPeriod(Period $period): int
    {
        return $period
            ->enrollments
            ->where('status_id', 2) // paid
            ->where('parent_id', null)
            ->count();
    }

    private function getPaidEnrollmentsCountForYear(Year $year)
    {
        $total = 0;

        foreach ($year->periods as $period) {
            $total += $this->getPaidEnrollmentsCountForPeriod($period);
        }

        return $total;
    }
}
