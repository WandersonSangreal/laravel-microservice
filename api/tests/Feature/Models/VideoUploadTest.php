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

    public function test_create_with_basic_fields()
    {
        $video = Video::create($this->data);

        $this->assertDatabaseHas(Video::class, $video->getOriginal());

    }

    public function test_create_with_files()
    {
        Storage::fake();

        $files = [
            'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
            'video_file' => UploadedFile::fake()->create('video.mp4')->size(1),
            'banner_file' => UploadedFile::fake()->image('banner.jpg'),
            'trailer_file' => UploadedFile::fake()->create('trailer.mp4')->size(1),
        ];

        $video = Video::create($this->data + $files);

        Storage::assertExists("{$video->id}/{$video->thumb_file}");
        Storage::assertExists("{$video->id}/{$video->video_file}");
        Storage::assertExists("{$video->id}/{$video->banner_file}");
        Storage::assertExists("{$video->id}/{$video->trailer_file}");
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
                'video_file' => UploadedFile::fake()->create('video.mp4')->size(1),
                'banner_file' => UploadedFile::fake()->image('banner.jpg'),
                'trailer_file' => UploadedFile::fake()->create('trailer.mp4')->size(1),
            ];

            Video::create($this->data + $files);

        } catch (\Exception $exception) {

            $this->assertCount(0, Storage::allFiles());

            $hasError = true;

        }

        $this->assertTrue($hasError);

    }

    public function test_update_with_basic_fields()
    {
        $video = Video::factory()->create();

        $video->update($this->data);

        $this->assertDatabaseHas(Video::class, $video->getOriginal());

        $this->assertTrue($video->title === $this->data['title']);

    }

    public function test_update_with_files()
    {

        Storage::fake();

        $video = Video::factory()->create();

        $files = [
            'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
            'video_file' => UploadedFile::fake()->create('video.mp4')->size(1),
            'banner_file' => UploadedFile::fake()->image('banner.jpg'),
            'trailer_file' => UploadedFile::fake()->create('trailer.mp4')->size(1),
        ];

        $video->update($this->data + $files);

        Storage::assertExists("{$video->id}/{$video->thumb_file}");
        Storage::assertExists("{$video->id}/{$video->video_file}");
        Storage::assertExists("{$video->id}/{$video->banner_file}");
        Storage::assertExists("{$video->id}/{$video->trailer_file}");

        $newVideoFile = UploadedFile::fake()->create('video.mp4');

        $video->update($this->data + ['video_file' => $newVideoFile]);

        Storage::assertExists("{$video->id}/{$files['thumb_file']->hashName()}");
        Storage::assertExists("{$video->id}/{$files['banner_file']->hashName()}");
        Storage::assertExists("{$video->id}/{$files['trailer_file']->hashName()}");
        Storage::assertExists("{$video->id}/{$newVideoFile->hashName()}");
        Storage::assertMissing("{$video->id}/{$files['video_file']->hashName()}");

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
                'video_file' => UploadedFile::fake()->create('video.mp4')->size(1),
                'banner_file' => UploadedFile::fake()->image('banner.jpg'),
                'trailer_file' => UploadedFile::fake()->create('trailer.mp4')->size(1),
            ];

            $video->update($this->data + $files);

        } catch (\Exception $exception) {

            $this->assertCount(0, Storage::allFiles());

            $hasError = true;

        }

        $this->assertTrue($hasError);

    }

}
