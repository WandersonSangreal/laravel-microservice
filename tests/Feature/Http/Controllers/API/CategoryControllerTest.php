<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_index()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([$category->toArray()]);
    }

    public function test_show()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response->assertStatus(200)->assertJson($category->toArray());
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([Lang::get('validation.required', ['attribute' => 'name'])]);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])]);
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([Lang::get('validation.boolean', ['attribute' => 'is active'])]);
    }

    public function test_invalidation_values()
    {
        /**
         * CREATE
         */

        $response = $this->json('POST', route('categories.store'));

        $this->assertInvalidationRequired($response);

        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        /**
         * UPDATE
         */

        $category = Category::factory()->create();

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

    }

    public function test_store()
    {

        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test'
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response->assertStatus(201)->assertJson($category->toArray());

        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ]);

        $response->assertJsonFragment([
            'description' => 'description',
            'is_active' => false
        ]);

    }

    public function test_update()
    {
        $category = Category::factory()->create(['is_active' => false, 'description' => 'description']);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'description' => 'test',
            'is_active' => true
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response->assertStatus(200)->assertJson($category->toArray())->assertJsonFragment([
            'description' => 'test',
            'is_active' => true
        ]);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'description' => ''
        ]);

        $response->assertJsonFragment([
            'description' => null
        ]);

    }

    public function test_destroy()
    {
        $category = Category::factory()->create();

        $response = $this->json('DELETE', route('categories.destroy', ['category' => $category->id]));

        $response->assertStatus(204);

        $this->assertSoftDeleted($category);

    }

}
