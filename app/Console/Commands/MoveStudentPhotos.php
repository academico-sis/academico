<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Student photos used to be stored on the public disk, where anyone could download them. New photos are stored
 * on the private disk; this command moves the existing ones there.
 */
class MoveStudentPhotos extends Command
{
    protected $signature = 'academico:move-student-photos';

    protected $description = 'Move the student photos stored on a public disk to the private disk';

    public function handle(): int
    {
        $photos = Media::query()
            ->where('model_type', (new Student)->getMorphClass())
            ->where('collection_name', 'profile-picture')
            ->where('disk', '!=', 'local')
            ->get();

        if ($photos->isEmpty()) {
            $this->info('Every student photo is on the private disk.');

            return Command::SUCCESS;
        }

        $failures = 0;

        foreach ($photos as $photo) {
            $student = $photo->model;

            if (! $student) {
                $this->warn("Photo {$photo->id} belongs to no student and was left on the {$photo->disk} disk.");
                $failures++;

                continue;
            }

            try {
                $photo->move($student, 'profile-picture', 'local');
            } catch (\Throwable $exception) {
                report($exception);
                $this->error("Photo {$photo->id} of student {$student->id} could not be moved: {$exception->getMessage()}");
                $failures++;
            }
        }

        $this->info('Photos moved to the private disk: '.($photos->count() - $failures));

        return $failures === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
