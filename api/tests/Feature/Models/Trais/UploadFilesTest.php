<?php

namespace Tests\Feature\Models\Trais;

use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Support\Facades\Event;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UploadFilesTest extends TestCase
{
    use DatabaseMigrations;

    private UploadFilesStub $upload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->upload = new UploadFilesStub();
    }

    public function test_make_old_files_on_saving()
    {
        UploadFilesStub::dropTable();
        UploadFilesStub::createTable();

        $this->upload->fill([
            'name' => 'test',
            'file1' => 'test1.mp4',
            'file2' => 'test2.mp4',
        ]);

        $this->upload->save();

        $this->assertCount(0, $this->upload->oldFiles);

        $this->upload->update([
            'name' => 'test_name',
            'file2' => 'test3.mp4',
        ]);

        $this->assertEqualsCanonicalizing(['test2.mp4'], $this->upload->oldFiles);

    }

    public function test_make_old_files_null_on_saving()
    {
        UploadFilesStub::dropTable();
        UploadFilesStub::createTable();

        $this->upload->fill([
            'name' => 'test'
        ]);

        $this->upload->save();

        $this->upload->update([
            'name' => 'test_name',
            'file2' => 'test3.mp4',
        ]);

        $this->assertEqualsCanonicalizing([], $this->upload->oldFiles);

    }

}
