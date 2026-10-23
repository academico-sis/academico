<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Partner;
use App\Models\Period;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StatService
{
    /** Courses often have only one of the two volumes: a NULL operand would turn the whole sum into NULL */
    private const COURSE_VOLUME = 'COALESCE(courses.volume, 0) + COALESCE(courses.remote_volume, 0)';

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
        return (int) $this->coursesQuery->whereNull('parent_course_id')->sum(DB::raw(self::COURSE_VOLUME));
    }

    public function soldHoursCount(): int
    {
        if ($this->external) {
            return (int) $this->coursesQuery->sum(DB::raw('('.self::COURSE_VOLUME.') * head_count'));
        }

        $courseIds = $this->coursesQuery->pluck('courses.id');

        if ($courseIds->isEmpty()) {
            return 0;
        }

        return (int) DB::table('courses')
            ->leftJoin(DB::raw('(SELECT course_id, COUNT(*) as cnt FROM enrollments WHERE status_id IN (1, 2) AND parent_id IS NULL GROUP BY course_id) as ec'), 'courses.id', '=', 'ec.course_id')
            ->whereIn('courses.id', $courseIds)
            ->sum(DB::raw('('.self::COURSE_VOLUME.') * COALESCE(ec.cnt, 0)'));
    }

    public function newStudentsCount(): int
    {
        if ($this->reference::class !== Period::class) {
            return 0;
        }

        return $this->reference->newStudents()->count();
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
        if ($this->partner) {
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
        return $period->enrollments()->where('status_id', 1)->whereNull('parent_id')->count();
    }

    private function getPendingEnrollmentsCountForYear(Year $year): int
    {
        return DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->join('periods', 'courses.period_id', '=', 'periods.id')
            ->where('periods.year_id', $year->id)
            ->where('enrollments.status_id', 1)
            ->whereNull('enrollments.parent_id')
            ->count();
    }

    private function getPaidEnrollmentsCountForPeriod(Period $period): int
    {
        return $period->enrollments()->where('status_id', 2)->whereNull('parent_id')->count();
    }

    private function getPaidEnrollmentsCountForYear(Year $year): int
    {
        return DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->join('periods', 'courses.period_id', '=', 'periods.id')
            ->where('periods.year_id', $year->id)
            ->where('enrollments.status_id', 2)
            ->whereNull('enrollments.parent_id')
            ->count();
    }
}
