<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestResource;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResource;

    private $category;
    private array $serialized = [
        'id',
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create();
    }

    public function test_index()
    {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJsonStructure(['data' => ['*' => $this->serialized], 'meta' => [], 'links' => []]);
    }

    public function test_show()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));

        $response->assertStatus(200)->assertJsonStructure(['data' => $this->serialized]);

        $id = $response->json('data.id');

        $resource = new CategoryResource(Category::find($id));

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

    }

    public function test_store()
    {
        $data = ['name' => 'test'];

        $response = $this->assertStore($data, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null]);

        $response->assertJsonStructure(['data' => $this->serialized]);

        $data = ['name' => 'test', 'description' => 'description', 'is_active' => false];

        $this->assertStore($data, $data + ['description' => 'description', 'is_active' => false]);

        # $resourceData = (new CategoryResource(Category::first()))->response()->getData(true);
        # $response->assertJson($resourceData);

        $id = $response->json('data.id');

        $resource = new CategoryResource(Category::find($id));

        $this->assetResource($response, $resource);

    }

    public function test_update()
    {
        $this->category = Category::factory()->create(['is_active' => false, 'description' => 'description']);

        $data = ['name' => 'test', 'description' => 'test', 'is_active' => true];

        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);

        $response->assertJsonStructure(['data' => $this->serialized]);

        $id = $response->json('data.id');

        $resource = new CategoryResource(Category::find($id));

        $this->assetResource($response, $resource);

        $data['description'] = '';

        $this->assertUpdate($data, array_merge($data, ['description' => null]));

        $data['description'] = 'test';

        $this->assertUpdate($data, array_merge($data, ['description' => 'test']));

        $data['description'] = null;

        $this->assertUpdate($data, array_merge($data, ['description' => null]));

    }

    public function test_destroy()
    {
        $response = $this->json('DELETE', route('categories.destroy', ['category' => $this->category->id]));

        $response->assertStatus(204);

        $this->assertSoftDeleted($this->category);
    }

    protected function routeStore(): string
    {
        return route('categories.store');
    }

    protected function routeUpdate(): string
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model(): string
    {
        return Category::class;
    }

}
