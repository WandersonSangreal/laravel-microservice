<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();

        $this->castMember = CastMember::factory()->create();
    }

    public function test_index()
    {
        $response = $this->get(route('cast_members.index'));

        $response->assertStatus(200)->assertJson([$this->castMember->toArray()]);
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
