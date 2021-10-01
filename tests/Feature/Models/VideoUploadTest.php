<?php

namespace Tests\Feature\Models;

use App\Http\Controllers\API\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

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



}
