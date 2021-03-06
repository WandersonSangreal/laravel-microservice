<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Http\Resources\VideoResource;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\TestResource;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResource;

    private $video;
    private $sendValue;
    private array $serialized = [
        'id',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'thumb_file',
        'banner_file',
        'video_file',
        'trailer_file',
        'deleted_at',
        'created_at',
        'updated_at',
        'video_file_url',
        'thumb_file_url',
        'banner_file_url',
        'trailer_file_url',
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        ],
        'genres' => [
            '*' => [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ]
        ]
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = Video::factory()->create();
        $this->sendValue = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
        ];
    }

    public function test_index()
    {
        $response = $this->get(route('videos.index'));

        $response->assertStatus(200)->assertJsonStructure(['data' => ['*' => $this->serialized], 'meta' => [], 'links' => []]);
    }

    public function test_show()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response->assertStatus(200)->assertJsonStructure(['data' => $this->serialized]);

        $id = $response->json('data.id');

        $resource = new VideoResource(Video::find($id));

        $this->assetResource($response, $resource);

    }

    public function test_invalidation_required()
    {

        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];

        $this->assertInvalidationStoreAction($data, 'required');
        $this->assertInvalidationUpdateAction($data, 'required');

    }

    public function test_invalidation_file()
    {

        $data = ['video_file' => UploadedFile::fake()->create('teste.avi')];

        $this->assertInvalidationStoreAction($data, 'mimes', ['values' => 'mp4']);
        $this->assertInvalidationUpdateAction($data, 'mimes', ['values' => 'mp4']);

    }

    public function test_invalidation_file_size()
    {

        $data = ['video_file' => UploadedFile::fake()->create('teste.mp4')->size(52 * 1024 * 1024)];

        $this->assertInvalidationStoreAction($data, 'max.file', ['max' => Video::VIDEO_FILE_MAX_SIZE]);
        $this->assertInvalidationUpdateAction($data, 'max.file', ['max' => Video::VIDEO_FILE_MAX_SIZE]);

        $data = ['trailer_file' => UploadedFile::fake()->create('teste.mp4')->size(2 * 1024 * 1024)];

        $this->assertInvalidationStoreAction($data, 'max.file', ['max' => Video::TRAILER_FILE_MAX_SIZE]);
        $this->assertInvalidationUpdateAction($data, 'max.file', ['max' => Video::TRAILER_FILE_MAX_SIZE]);

        $data = ['thumb_file' => UploadedFile::fake()->image('teste.jpg')->size(6 * 1024)];

        $this->assertInvalidationStoreAction($data, 'max.file', ['max' => Video::THUMB_FILE_MAX_SIZE]);
        $this->assertInvalidationUpdateAction($data, 'max.file', ['max' => Video::THUMB_FILE_MAX_SIZE]);

        $data = ['banner_file' => UploadedFile::fake()->image('teste.jpg')->size(12 * 1024)];

        $this->assertInvalidationStoreAction($data, 'max.file', ['max' => Video::BANNER_FILE_MAX_SIZE]);
        $this->assertInvalidationUpdateAction($data, 'max.file', ['max' => Video::BANNER_FILE_MAX_SIZE]);


    }

    public function test_invalidation_max()
    {

        $data = ['title' => str_repeat('a', 256)];

        $this->assertInvalidationStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationUpdateAction($data, 'max.string', ['max' => 255]);

    }

    public function test_invalidation_integer()
    {

        $data = ['duration' => 's'];

        $this->assertInvalidationStoreAction($data, 'integer');
        $this->assertInvalidationUpdateAction($data, 'integer');

    }

    public function test_invalidation_year_launched_field()
    {

        $data = ['year_launched' => 'a'];

        $this->assertInvalidationStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationUpdateAction($data, 'date_format', ['format' => 'Y']);

    }

    public function test_invalidation_opened_field()
    {

        $data = ['opened' => 's'];

        $this->assertInvalidationStoreAction($data, 'boolean');
        $this->assertInvalidationUpdateAction($data, 'boolean');

    }

    public function test_invalidation_rating_field()
    {

        $data = ['rating' => 0];

        $this->assertInvalidationStoreAction($data, 'in');
        $this->assertInvalidationUpdateAction($data, 'in');

    }

    public function test_invalidation_categories_field()
    {

        $data = ['categories_id' => 'a'];

        $this->assertInvalidationStoreAction($data, 'array');
        $this->assertInvalidationUpdateAction($data, 'array');

        $data = ['categories_id' => [100]];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

    }

    public function test_invalidation_genres_field()
    {

        $data = ['genres_id' => 'a'];

        $this->assertInvalidationStoreAction($data, 'array');
        $this->assertInvalidationUpdateAction($data, 'array');

        $data = ['genres_id' => [100]];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

    }

    public function test_soft_delete_exists()
    {
        $genre = Genre::factory()->create();
        $genre->delete();

        $data = ['genres_id' => [$genre->id]];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

        $category = Category::factory()->create();
        $category->delete();

        $data = ['categories_id' => [$category->id]];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

    }

    public function test_save()
    {
        Storage::fake();

        $category = Category::factory()->create();
        $genre = Genre::factory()->create();

        $genre->categories()->sync((array)$category->id);

        $data = [
            [
                'send_data' => $this->sendValue + ['categories_id' => [$category->id], 'genres_id' => [$genre->id]],
                'test_data' => $this->sendValue + ['opened' => false]
            ],
            [
                'send_data' => $this->sendValue + ['opened' => true, 'categories_id' => [$category->id], 'genres_id' => [$genre->id]],
                'test_data' => $this->sendValue + ['opened' => true]
            ],
            [
                'send_data' => $this->sendValue + ['rating' => Video::RATING_LIST[1], 'categories_id' => [$category->id], 'genres_id' => [$genre->id]],
                'test_data' => $this->sendValue + ['rating' => Video::RATING_LIST[1]]
            ],
        ];

        foreach ($data as $value) {

            $complements = ['deleted_at' => null, 'video_file' => null, 'thumb_file' => null, 'banner_file' => null, 'trailer_file' => null];

            $response = $this->assertStore($value['send_data'], $value['test_data'] + $complements);

            $response->assertJsonStructure(['data' => $this->serialized]);

            $id = $response->json('data.id');

            $this->assertHasCategory($id, current($value['send_data']['categories_id']));

            $this->assertHasGenre($id, current($value['send_data']['genres_id']));

            # $response = $this->assertStore($value['send_data'], $value['test_data'] + ['deleted_at' => null, 'thumb_file' => null, 'video_file' => null, 'banner_file' => null, 'trailer_file' => null]);

            $response->assertJsonStructure(['data' => $this->serialized]);

            $id = $response->json('data.id');

            $resource = new VideoResource(Video::find($id));

            $this->assetResource($response, $resource);

        }

    }

    public function test_destroy()
    {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]));

        $response->assertStatus(204);

        $this->assertSoftDeleted($this->video);
    }

    public function test_sync_categories()
    {
        $categoriesID = Category::factory(3)->create()->pluck('id')->toArray();
        $genre = Genre::factory()->create();

        $genre->categories()->sync($categoriesID);
        $genreID = $genre->id;

        $response = $this->json('POST', $this->routeStore(), $this->sendValue + ['categories_id' => [$categoriesID[0]], 'genres_id' => [$genreID]]);

        $id = $response->json('data.id');

        $this->assertDatabaseHas('category_video', ['category_id' => $categoriesID[0], 'video_id' => $id]);

        $response = $this->json('PUT', route('videos.update', ['video' => $id]), $this->sendValue + ['categories_id' => [$categoriesID[1], $categoriesID[2]], 'genres_id' => [$genreID]]);

        $this->assertDatabaseMissing('category_video', ['category_id' => $categoriesID[0], 'video_id' => $id]);

        $this->assertDatabaseHas('category_video', ['category_id' => $categoriesID[1], 'video_id' => $id]);

        $this->assertDatabaseHas('category_video', ['category_id' => $categoriesID[2], 'video_id' => $id]);

    }

    public function test_sync_genres()
    {
        $genres = Genre::factory(3)->create();
        $genresID = $genres->pluck('id')->toArray();
        $categoryID = Category::factory()->create()->id;

        $genres->each(function ($genre) use ($categoryID) {
            $genre->categories()->sync($categoryID);
        });

        $response = $this->json('POST', $this->routeStore(), $this->sendValue + ['categories_id' => [$categoryID], 'genres_id' => [$genresID[0]]]);

        $id = $response->json('data.id');

        $this->assertDatabaseHas('genre_video', ['genre_id' => $genresID[0], 'video_id' => $id]);

        $this->json('PUT', route('videos.update', ['video' => $id]), $this->sendValue + ['categories_id' => [$categoryID], 'genres_id' => [$genresID[1], $genresID[2]]]);

        $this->assertDatabaseMissing('genre_video', ['genre_id' => $genresID[0], 'video_id' => $id]);

        $this->assertDatabaseHas('genre_video', ['genre_id' => $genresID[1], 'video_id' => $id]);

        $this->assertDatabaseHas('genre_video', ['genre_id' => $genresID[2], 'video_id' => $id]);

    }

    protected function assertHasCategory($videoID, $categoryID)
    {
        $this->assertDatabaseHas('category_video', ['video_id' => $videoID, 'category_id' => $categoryID]);
    }

    protected function assertHasGenre($videoID, $genreID)
    {
        $this->assertDatabaseHas('genre_video', ['video_id' => $videoID, 'genre_id' => $genreID]);
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
