<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function test_list()
    {
        $keys = ['id', 'name', 'is_active', 'created_at', 'updated_at', 'deleted_at'];

        Genre::factory(1)->create();

        $genders = Genre::all();

        $this->assertCount(1, $genders);

        $genderKey = array_keys($genders->first()->getAttributes());

        $this->assertEqualsCanonicalizing($keys, $genderKey);

    }

    public function test_create()
    {
        $gender = Genre::create(['name' => 'test']);
        $gender->refresh();

        $this->assertEquals('test', $gender->name);
        $this->assertTrue($gender->is_active);
        $this->assertTrue(Uuid::isValid($gender->id));

        $gender = Genre::create(['name' => 'test', 'is_active' => false]);
        $this->assertFalse($gender->is_active);

        $gender = Genre::create(['name' => 'test', 'is_active' => true]);
        $this->assertTrue($gender->is_active);
    }

    public function test_update()
    {
        $create = ['is_active' => false];
        $values = ['name' => 'test_name_updated', 'is_active' => true];

        $gender = Genre::factory()->create($create)->first();

        $gender->update($values);

        foreach ($values as $key => $value) {
            $this->assertEquals($value, $gender->{$key});
        }

    }

    public function test_delete()
    {
        $gender = Genre::factory(1)->create()->first();

        $this->assertCount(1, Genre::all());

        $gender->delete();

        $this->assertCount(0, Genre::all());

        $this->assertCount(1, Genre::onlyTrashed()->get());

    }
}
