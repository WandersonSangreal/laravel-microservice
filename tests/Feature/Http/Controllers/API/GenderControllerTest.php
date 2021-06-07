<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Gender;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class GenderControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_index()
    {
        $gender = Gender::factory()->create();

        $response = $this->get(route('genders.index'));

        $response->assertStatus(200)->assertJson([$gender->toArray()]);
    }

    public function test_show()
    {
        $gender = Gender::factory()->create();

        $response = $this->get(route('genders.show', ['gender' => $gender->id]));

        $response->assertStatus(200)->assertJson($gender->toArray());
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

        $response = $this->json('POST', route('genders.store'));

        $this->assertInvalidationRequired($response);

        $response = $this->json('POST', route('genders.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        /**
         * UPDATE
         */

        $gender = Gender::factory()->create();

        $response = $this->json('PUT', route('genders.update', ['gender' => $gender->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

    }

    public function test_store()
    {

        $response = $this->json('POST', route('genders.store'), [
            'name' => 'test'
        ]);

        $id = $response->json('id');
        $gender = Gender::find($id);

        $response->assertStatus(201)->assertJson($gender->toArray());

        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', route('genders.store'), [
            'name' => 'test',
            'is_active' => false
        ]);

        $response->assertJsonFragment([
            'is_active' => false
        ]);

    }

    public function test_update()
    {
        $gender = Gender::factory()->create(['name' => 'test', 'is_active' => false]);

        $response = $this->json('PUT', route('genders.update', ['gender' => $gender->id]), [
            'name' => 'test_world',
            'is_active' => false
        ]);

        $id = $response->json('id');
        $gender = Gender::find($id);

        $response->assertStatus(200)->assertJson($gender->toArray())->assertJsonFragment([
            'name' => 'test_world',
            'is_active' => false
        ]);

    }

    public function test_destroy()
    {
        $gender = Gender::factory()->create();

        $response = $this->json('DELETE', route('genders.destroy', ['gender' => $gender->id]));

        $response->assertStatus(204);

        $this->assertSoftDeleted($gender);

    }
}
