<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class CachedReport implements Arrayable
{
    const TABLE_NAME = 'cached_reports';

    public Carbon $updated_at;

    public function __construct(
        public readonly string $periodName,
        public readonly ?int $yearId,
        public readonly ?int $periodId,
        public readonly int $students,
        public readonly int $enrollments,
        public readonly ?int $acquisitionRate,
        public readonly ?int $newStudents,
        public readonly int $taughtHours,
        public readonly int $soldHours,
        public readonly ?int $takings,
        public readonly ?int $avgTakings,
        public readonly ?int $order = null,
    ) {
        $this->updated_at = Carbon::now();
    }

    public function toArray(): array
    {
        return [
            'updated_at' => $this->updated_at->toDateTime(),
            'period_name' => $this->periodName,
            'year_id' => $this->yearId,
            'period_id' => $this->periodId,
            'students' => $this->students,
            'enrollments' => $this->enrollments,
            'acquisition_rate' => $this->acquisitionRate,
            'new_students' => $this->newStudents,
            'taught_hours' => $this->taughtHours,
            'sold_hours' => $this->soldHours,
            'takings' => $this->takings,
            'avg_takings' => $this->avgTakings,
            'order' => $this->order,
        ];
    }

    public static function from(\stdClass $data): self
    {
        return new self(
            periodName: $data->period_name,
            yearId: $data->year_id,
            periodId: $data->period_id,
            students: $data->students,
            enrollments: $data->enrollments,
            acquisitionRate: $data->acquisition_rate,
            newStudents: $data->new_students,
            taughtHours: $data->taught_hours,
            soldHours: $data->sold_hours,
            takings: $data->takings,
            avgTakings: $data->avg_takings,
            order: $data->order,
        );
    }
}
