<?php

namespace Tests\Prod\Models\Traits;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\Traits\TestStorages;

class UploadFilesProdTest extends TestCase
{
    use TestStorages;

    private UploadFilesStub $upload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->upload = new UploadFilesStub();

        Config::set('filesystems.default', 'gcs');

        $this->deleteAllFiles();

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

    public function test_delete_old_files()
    {
        Storage::fake();

        $file1 = UploadedFile::fake()->create('video1.mp4')->size(1);
        $file2 = UploadedFile::fake()->create('video2.mp4')->size(1);

        $this->upload->uploadFiles([$file1, $file2]);
        $this->upload->deleteOldFiles();

        $this->assertCount(2, Storage::allFiles());

        $this->upload->oldFiles = [$file1->hashName()];
        $this->upload->deleteOldFiles();

        Storage::assertMissing($file1->hashName());
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

}
