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

class VideoTest extends TestCase
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

    public function test_list()
    {
        Video::factory()->create();

        $videos = Video::all();
        $this->assertCount(1, $videos);
        $videosKeys = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing(['id', 'title', 'description', 'year_launched', 'opened', 'rating', 'video_file', 'thumb_file', 'duration', 'created_at', 'updated_at', 'deleted_at'], $videosKeys);

    }

    public function test_create_with_basic_fields()
    {
        $video = Video::create($this->data);
        $video->refresh();

        $this->assertEquals(36, strlen($video->id));
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => false]);

        $video = Video::create($this->data + ['opened' => true]);
        $video->refresh();

        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => true]);
    }

    public function test_create_with_relations()
    {
        $category = Category::factory()->create();
        $genre = Genre::factory()->create();

        $video = Video::create($this->data + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function test_update_with_basic_fields()
    {
        $video = Video::factory()->create(['opened' => false]);
        $video->update($this->data);

        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => false]);

        $video = Video::factory()->create(['opened' => false]);
        $video->update($this->data + ['opened' => true]);

        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => true]);
    }

    public function test_update_with_relations()
    {
        $video = Video::factory()->create();

        $category = Category::factory()->create();
        $genre = Genre::factory()->create();

        $video->update($this->data + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function test_rollback_create()
    {
        $hasError = false;

        try {

            Video::create($this->data + ['categories_id' => [0, 1, 2]]);

        } catch (QueryException $queryException) {

            $hasError = true;
            $this->assertCount(0, Video::all());

        }

        $this->assertTrue($hasError);

    }

    public function test_rollback_update()
    {
        $hasError = false;

        $video = Video::factory()->create();

        $title = $video->title;

        try {

            $video->update($this->data + ['title' => 'new.title', 'categories_id' => [0, 1, 2]]);

        } catch (QueryException $queryException) {

            $hasError = true;
            $this->assertDatabaseHas('videos', ['title' => $title]);

        }

        $this->assertTrue($hasError);

    }

    public function test_handle_realtions()
    {
        $video = Video::factory()->create();
        Video::handleRelations($video, []);
        $this->assertCount(0, $video->categories);
        $this->assertCount(0, $video->genres);

        $category = Category::factory()->create();
        Video::handleRelations($video, ['categories_id' => $category->id]);
        $video->refresh();
        $this->assertCount(1, $video->categories);

        $genre = Genre::factory()->create();
        Video::handleRelations($video, ['genres_id' => $genre->id]);
        $video->refresh();
        $this->assertCount(1, $video->genres);

        $video->categories()->delete();
        $video->genres()->delete();
        Video::handleRelations($video, [['categories_id' => $category->id], ['genres_id' => $genre->id]]);
        $video->refresh();
        $this->assertCount(1, $video->categories);
        $this->assertCount(1, $video->genres);
    }

    protected function assertHasCategory($videoID, $categoryID)
    {
        $this->assertDatabaseHas('category_video', ['video_id' => $videoID, 'category_id' => $categoryID]);
    }

    protected function assertHasGenre($videoID, $genreID)
    {
        $this->assertDatabaseHas('genre_video', ['video_id' => $videoID, 'genre_id' => $genreID]);
    }

}
