<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Http\Controllers\API\GenreController;
use App\Http\Resources\GenreResource;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;
use Mockery;
use Tests\TestCase;
use Tests\Traits\TestResource;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResource;

    private $genre;
    private array $serialized = [
        'id',
        'name',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->genre = Genre::factory()->create();
    }

    public function test_index()
    {
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)->assertJsonStructure(['data' => ['*' => $this->serialized], 'meta' => [], 'links' => []]);
    }

    public function test_show()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));

        $response->assertStatus(200)->assertJsonStructure(['data' => $this->serialized]);

        $id = $response->json('data.id');

        $resource = new GenreResource(Genre::find($id));

        $this->assetResource($response, $resource);

    }

    public function test_invalidation_values()
    {

        $data = ['name' => ''];
        $this->assertInvalidationStoreAction($data, 'required');
        $this->assertInvalidationUpdateAction($data, 'required');

        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationUpdateAction($data, 'max.string', ['max' => 255]);

        $data = ['is_active' => 'a'];
        $this->assertInvalidationStoreAction($data, 'boolean');
        $this->assertInvalidationUpdateAction($data, 'boolean');

        $data = ['categories_id' => 'a'];
        $this->assertInvalidationStoreAction($data, 'array');
        $this->assertInvalidationUpdateAction($data, 'array');

        $data = ['categories_id' => ['teste']];
        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

        $category = Category::factory()->create();
        $category->delete();
        $data = ['categories_id' => [$category->id]];
        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

    }

    public function test_store()
    {
        $categoryID = Category::factory()->create()->id;

        $data = ['name' => 'test'];

        $response = $this->assertStore($data + ['categories_id' => [$categoryID]], $data + ['is_active' => true, 'deleted_at' => null]);

        $response->assertJsonStructure(['data' => $this->serialized]);

        $data = ['name' => 'test', 'is_active' => false];

        $this->assertStore($data + ['categories_id' => [$categoryID]], $data + ['is_active' => false]);

        $id = $response->json('data.id');

        $this->assetHasCategory($id, $categoryID);

        $resource = new GenreResource(Genre::find($id));

        $this->assetResource($response, $resource);

    }

    public function test_update()
    {
        $categoryID = Category::factory()->create()->id;

        $this->genre = Genre::factory()->create(['name' => 'test', 'is_active' => false]);

        $data = ['name' => 'test_world', 'is_active' => true];

        $response = $this->assertUpdate($data + ['categories_id' => [$categoryID]], $data + ['deleted_at' => null]);

        $response->assertJsonStructure(['data' => $this->serialized]);

        $data['name'] = 'hello';

        $this->assertUpdate($data + ['categories_id' => [$categoryID]], $data);

        $data['is_active'] = false;

        $response = $this->assertUpdate($data + ['categories_id' => [$categoryID]], $data);

        $this->assetHasCategory($response->json('data.id'), $categoryID);

        $resource = new GenreResource(Genre::find($response->json('data.id')));

        $this->assetResource($response, $resource);

    }

    public function test_destroy()
    {
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $this->genre->id]));

        $response->assertStatus(204);

        $this->assertSoftDeleted($this->genre);

    }

    public function test_sync_categories()
    {
        $categoriesID = Category::factory(3)->create()->pluck('id')->toArray();

        $sendValues = ['name' => 'test', 'categories_id' => [$categoriesID[0]]];

        $response = $this->json('POST', $this->routeStore(), $sendValues);

        $id = $response->json('data.id');

        $this->assertDatabaseHas('category_genre', ['category_id' => $categoriesID[0], 'genre_id' => $id]);

        $sendValues = ['name' => 'test', 'categories_id' => [$categoriesID[1], $categoriesID[2]]];

        $this->json('PUT', route('genres.update', ['genre' => $id]), $sendValues);

        $this->assertDatabaseMissing('category_genre', ['category_id' => $categoriesID[0], 'genre_id' => $id]);

        $this->assertDatabaseHas('category_genre', ['category_id' => $categoriesID[1], 'genre_id' => $id]);

        $this->assertDatabaseHas('category_genre', ['category_id' => $categoriesID[2], 'genre_id' => $id]);

    }

    protected function assetHasCategory($genre, $category)
    {
        $this->assertDatabaseHas('category_genre', ['genre_id' => $genre, 'category_id' => $category]);
    }

    protected function routeStore(): string
    {
        return route('genres.store');
    }

    protected function routeUpdate(): string
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    protected function model(): string
    {
        return Genre::class;
    }

}
