<?php

namespace Tests\Feature;

use App\Livewire\StudentAccount;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class StudentPhotosTest extends TestCase
{
    use RefreshDatabase;

    private string $privateRoot;

    /**
     * The private disk keeps its real driver, so that its URLs are signed, but in a temporary directory.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->privateRoot = storage_path('framework/testing/disks/student-photos');
        config(['filesystems.disks.local.root' => $this->privateRoot]);
        Storage::forgetDisk('local');
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->privateRoot);

        parent::tearDown();
    }

    public function test_photos_are_stored_on_the_private_disk(): void
    {
        $student = Student::factory()->create();

        Livewire::actingAs($student->user)
            ->test(StudentAccount::class)
            ->set('photo', UploadedFile::fake()->image('photo.jpg'))
            ->call('savePhoto')
            ->assertHasNoErrors();

        $media = $student->getFirstMedia('profile-picture');
        $this->assertSame('local', $media->disk);
        Storage::disk('local')->assertExists($media->getPathRelativeToRoot());
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_photos_are_only_served_with_a_signed_url(): void
    {
        $student = Student::factory()->create();
        $media = $student->addMedia(UploadedFile::fake()->image('photo.jpg'))->toMediaCollection('profile-picture');

        $url = $student->fresh()->image;

        $this->assertStringContainsString('signature=', $url);
        $this->get($url)->assertSuccessful();
        $this->assertStringContainsString('/private/', $url);
        $this->get(strtok($url, '?'))->assertForbidden();
        $this->get('/storage/'.$media->getPathRelativeToRoot())->assertNotFound();
    }

    public function test_photos_on_the_public_disk_are_moved_to_the_private_disk(): void
    {
        $student = Student::factory()->create();
        $student->addMedia(UploadedFile::fake()->image('photo.jpg'))->toMediaCollection('profile-picture', 'public');
        $this->assertNotEmpty(Storage::disk('public')->allFiles());

        $this->artisan('academico:move-student-photos')
            ->expectsOutput('Photos moved to the private disk: 1')
            ->assertSuccessful();

        $media = $student->fresh()->getFirstMedia('profile-picture');
        $this->assertSame('local', $media->disk);
        Storage::disk('local')->assertExists($media->getPathRelativeToRoot());
        $this->assertEmpty(Storage::disk('public')->allFiles());

        $this->artisan('academico:move-student-photos')
            ->expectsOutput('Every student photo is on the private disk.')
            ->assertSuccessful();
    }
}
