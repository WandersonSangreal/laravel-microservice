<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestResource;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResource;

    private $castMember;
    private array $serialized = [
        'id',
        'name',
        'type',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->castMember = CastMember::factory()->create();
    }

    public function test_index()
    {
        $response = $this->get(route('cast_members.index'));

        $response->assertStatus(200)->assertJsonStructure(['data' => ['*' => $this->serialized], 'meta' => [], 'links' => []]);
    }

    public function test_show()
    {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMember->id]));

        $response->assertStatus(200)->assertJsonStructure(['data' => $this->serialized]);

        $id = $response->json('data.id');

        $resource = new CastMemberResource(CastMember::find($id));

        $this->assetResource($response, $resource);

    }

    public function test_invalidation_values()
    {

        $data = ['name' => '', 'type' => ''];
        $this->assertInvalidationStoreAction($data, 'required');
        $this->assertInvalidationUpdateAction($data, 'required');

        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationUpdateAction($data, 'max.string', ['max' => 255]);

        $data = ['type' => 3];
        $this->assertInvalidationStoreAction($data, 'between.numeric', ['min' => 1, 'max' => 2]);
        $this->assertInvalidationUpdateAction($data, 'between.numeric', ['min' => 1, 'max' => 2]);

        $data = ['type' => 0];
        $this->assertInvalidationStoreAction($data, 'between.numeric', ['min' => 1, 'max' => 2]);
        $this->assertInvalidationUpdateAction($data, 'between.numeric', ['min' => 1, 'max' => 2]);

    }

    public function test_store()
    {
        $data = ['name' => 'test', 'type' => 1];

        $response = $this->assertStore($data, $data + ['deleted_at' => null]);

        $response->assertJsonStructure(['data' => $this->serialized]);

        $data = ['name' => 'test', 'type' => 2];

        $this->assertStore($data, $data);
    }

    public function test_update()
    {
        $data = ['name' => 'test', 'type' => CastMember::TYPE_DIRECTOR];

        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);

        $response->assertJsonStructure(['data' => $this->serialized]);

        $data['type'] = CastMember::TYPE_ACTOR;

        $this->assertUpdate($data, $data);

        $data['name'] = 'test_name';

        $this->assertUpdate($data, $data);

    }

    public function test_destroy()
    {
        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => $this->castMember->id]));

        $response->assertStatus(204);

        $this->assertSoftDeleted($this->castMember);
    }

    protected function routeStore(): string
    {
        return route('cast_members.store');
    }

    protected function routeUpdate(): string
    {
        return route('cast_members.update', ['cast_member' => $this->castMember->id]);
    }

    protected function model(): string
    {
        return CastMember::class;
    }
}
