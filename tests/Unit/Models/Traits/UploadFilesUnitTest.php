<?php

namespace Tests\Unit\Models\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tests\Stubs\Models\UploadFilesStub;

class UploadFilesUnitTest extends TestCase
{
    private $upload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->upload = new UploadFilesStub();
    }

    public function test_upload_file()
    {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');

        $this->upload->uploadFile($file);

        Storage::assertExists($file->hashName());

    }

    public function test_upload_files()
    {
        Storage::fake();

        $file1 = UploadedFile::fake()->create('video1.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');

        $this->upload->uploadFiles([$file1, $file2]);

        Storage::assertExists($file1->hashName());
        Storage::assertExists($file2->hashName());

    }

    public function test_delete_file()
    {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');

        $this->upload->uploadFile($file);
        $this->upload->deleteFile($file);

        Storage::assertMissing($file->hashName());

    }

    public function test_delete_files()
    {
        Storage::fake();

        $file1 = UploadedFile::fake()->create('video1.mp4');
        $file2 = UploadedFile::fake()->create('video2.mp4');

        $this->upload->uploadFiles([$file1, $file2]);
        $this->upload->deleteFiles([$file1->hashName(), $file2]);

        Storage::assertMissing($file1->hashName());
        Storage::assertMissing($file2->hashName());

    }

    public function test_extract_files()
    {
        $attributes = [];

        $files = UploadFilesStub::extractFiles($attributes);

        $this->assertCount(0, $attributes);
        $this->assertCount(0, $files);

        $attributes = ['file1' => 'test'];

        $files = UploadFilesStub::extractFiles($attributes);

        $this->assertCount(1, $attributes);
        $this->assertEquals(['file1' => 'test'], $attributes);
        $this->assertCount(0, $files);

        $file1 = UploadedFile::fake()->create('video1.mp4');
        $attributes = ['file1' => $file1, 'other' => 'test'];

        $files = UploadFilesStub::extractFiles($attributes);

        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => $file1->hashName(), 'other' => 'test'], $attributes);
        $this->assertCount(1, $files);
        $this->assertEquals([$file1], $files);

    }

}
