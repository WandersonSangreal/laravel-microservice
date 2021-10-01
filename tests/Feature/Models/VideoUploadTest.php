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
            'video_file' => UploadedFile::fake()->image('video.mp4')
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
                'video_file' => UploadedFile::fake()->image('video.mp4')
            ];

            Video::create($this->data + $files);

        } catch (\Exception $exception) {

            $this->assertCount(0, Storage::allFiles());

            $hasError = true;

        }

        $this->assertTrue($hasError);

    }

}
