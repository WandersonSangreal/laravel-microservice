<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

class VideoControllerUploadTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestUploads;

    private $video;
    private array $sendValue;

    protected function setUp(): void
    {
        parent::setUp();

        $genre = Genre::factory()->create();
        $category = Category::factory()->create();
        $genre->categories()->sync($category->id);

        $this->sendValue = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
        ];

        $this->video = Video::factory()->create([
            'opened' => false,
            'thumb_file' => 'thumb.jpg',
            'banner_file' => 'banner.jpg',
            'video_file' => 'video.mp4',
            'trailer_file' => 'trailer.mp4',
        ]);
    }

    public function test_invalidation_thumb_field()
    {
        $this->assetInvalidationFile('thumb_file', 'jpg', Video::THUMB_FILE_MAX_SIZE, 'mimes', ['values' => 'jpg, jpeg, png']);
    }

    public function test_invalidation_banner_field()
    {
        $this->assetInvalidationFile('banner_file', 'jpg', Video::BANNER_FILE_MAX_SIZE, 'mimes', ['values' => 'jpg, jpeg, png']);
    }

    public function test_invalidation_trailer_field()
    {
        $this->assetInvalidationFile('trailer_file', 'mp4', Video::TRAILER_FILE_MAX_SIZE, 'mimes', ['values' => 'mp4']);
    }

    public function test_invalidation_video_field()
    {
        $this->assetInvalidationFile('video_file', 'mp4', Video::VIDEO_FILE_MAX_SIZE, 'mimes', ['values' => 'mp4']);
    }

    public function test_store_with_files()
    {
        Storage::fake();

        $files = $this->getFiles();

        $response = $this->json('POST', $this->routeStore(), $this->sendValue + $files);

        $response->assertStatus(201);

        $this->assertFilesOnPersist($response, $files);

    }

    public function test_update_with_files()
    {
        Storage::fake();
        $files = $this->getFiles();

        $response = $this->json(
            'PUT', $this->routeUpdate(), $this->sendValue + $files
        );
        $response->assertStatus(200);
        $this->assertFilesOnPersist($response, $files);

        $newFiles = [
            'thumb_file' => UploadedFile::fake()->create("thumb_file.jpg"),
            'video_file' => UploadedFile::fake()->create("video_file.mp4")
        ];

        $response = $this->json('PUT', $this->routeUpdate(), $this->sendValue + $newFiles);

        $response->assertStatus(200);

        $this->assertFilesOnPersist($response, Arr::except($files, ['thumb_file', 'video_file']) + $newFiles);

        $id = $response->json('id') ?? $response->json('data.id');

        $video = Video::find($id);

        Storage::assertMissing("{$video->id}/{$files['thumb_file']->hashName()}");
        Storage::assertMissing("{$video->id}/{$files['video_file']->hashName()}");
    }

    protected function getFiles()
    {
        return [
            'thumb_file' => UploadedFile::fake()->create("thumb_file.jpg"),
            'banner_file' => UploadedFile::fake()->create("banner_file.jpg"),
            'trailer_file' => UploadedFile::fake()->create("trailer_file.mp4"),
            'video_file' => UploadedFile::fake()->create("video_file.mp4")
        ];
    }

    protected function assertFilesOnPersist(TestResponse $response, $files)
    {
        $id = $response->json('id') ?? $response->json('data.id');

        $video = Video::find($id);

        $this->assertFilesExistsInStorage($video, $files);
    }

    protected function assertFilesUrlExists(Video $video, TestResponse $response)
    {
        $fileFields = Video::$fileFields;
        $data = $response->getContent();

        foreach ($fileFields as $field) {
            $file = $video->{$field};
            $this->assertEquals(
                Storage::url($video->relativeFilePath($file)),
                $data[$field . '_url']
            );
        }
    }

    protected function routeStore(): string
    {
        return route('videos.store');
    }

    protected function routeUpdate(): string
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function model(): string
    {
        return Video::class;
    }
}
