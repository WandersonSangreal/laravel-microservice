<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Http\Controllers\API\GenreController;
use App\Models\Category;
use App\Models\Genre;
use http\Env\Request;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;
use Mockery;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genre = Genre::factory()->create();
    }

    public function test_index()
    {
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)->assertJson([$this->genre->toArray()]);
    }

    public function test_show()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));

        $response->assertStatus(200)->assertJson($this->genre->toArray());
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

        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data = ['name' => 'test', 'is_active' => false];

        $this->assertStore($data + ['categories_id' => [$categoryID]], $data + ['is_active' => false]);

        $this->assetHasCategory($response->json('id'), $categoryID);
    }

    public function test_update()
    {
        $categoryID = Category::factory()->create()->id;

        $this->genre = Genre::factory()->create(['name' => 'test', 'is_active' => false]);

        $data = ['name' => 'test_world', 'is_active' => true];

        $response = $this->assertUpdate($data + ['categories_id' => [$categoryID]], $data + ['deleted_at' => null]);

        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data['name'] = 'hello';

        $this->assertUpdate($data + ['categories_id' => [$categoryID]], $data);

        $data['is_active'] = false;

        $this->assertUpdate($data + ['categories_id' => [$categoryID]], $data);

        $this->assetHasCategory($response->json('id'), $categoryID);

    }

    protected function assetHasCategory($genre, $category)
    {
        $this->assertDatabaseHas('category_genre', ['genre_id' => $genre, 'category_id' => $category]);
    }

    public function test_destroy()
    {
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $this->genre->id]));

        $response->assertStatus(204);

        $this->assertSoftDeleted($this->genre);

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
