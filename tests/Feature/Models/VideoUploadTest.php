<?php

namespace Tests\Feature\Models;

use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class VideoUploadTest extends TestCase
{
    use DatabaseMigrations;

    private array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
        ];
    }

    public function test_create_with_files()
    {
        Storage::fake();

        $files = [
            'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
            'video_file' => UploadedFile::fake()->create('video.mp4')->size()
        ];

        $video = Video::create($this->data + $files);

        Storage::assertExists("{$video->id}/{$video->thumb_file}");
        Storage::assertExists("{$video->id}/{$video->video_file}");
    }

    public function test_create_if_rollback_files()
    {

        Storage::fake();

        Event::listen(TransactionCommitted::class, function () {
            throw new \Exception();
        });

        $hasError = false;

        try {

            $files = [
                'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                'video_file' => UploadedFile::fake()->create('video.mp4')->size(1)
            ];

            Video::create($this->data + $files);

        } catch (\Exception $exception) {

            $this->assertCount(0, Storage::allFiles());

            $hasError = true;

        }

        $this->assertTrue($hasError);

    }

    public function test_update_with_files()
    {

        Storage::fake();

        $video = Video::factory()->create();

        $thumbFile = UploadedFile::fake()->image('thumb.jpg');
        $videoFile = UploadedFile::fake()->create('video.mp4')->size(1);

        $video->update($this->data + ['thumb_file' => $thumbFile, 'video_file' => $videoFile]);

        Storage::assertExists("{$video->id}/{$video->thumb_file}");
        Storage::assertExists("{$video->id}/{$video->video_file}");

        $newVideoFile = UploadedFile::fake()->create('video.mp4');

        $video->update($this->data + ['video_file' => $newVideoFile]);

        Storage::assertExists("{$video->id}/{$thumbFile->hashName()}");
        Storage::assertExists("{$video->id}/{$newVideoFile->hashName()}");
        Storage::assertMissing("{$video->id}/{$videoFile->hashName()}");

    }

    public function test_update_if_rollback_files()
    {

        Storage::fake();

        $video = Video::factory()->create();

        Event::listen(TransactionCommitted::class, function () {
            throw new \Exception();
        });

        $hasError = false;

        try {

            $files = [
                'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                'video_file' => UploadedFile::fake()->create('video.mp4')->size(1)
            ];

            $video->update($this->data + $files);

        } catch (\Exception $exception) {

            $this->assertCount(0, Storage::allFiles());

            $hasError = true;

        }

        $this->assertTrue($hasError);

    }

}
