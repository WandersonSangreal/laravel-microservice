<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function test_list()
    {
        $keys = ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'];

        Category::factory(1)->create();

        $categories = Category::all();

        $this->assertCount(1, $categories);

        $categoryKey = array_keys($categories->first()->getAttributes());

        $this->assertEqualsCanonicalizing($keys, $categoryKey);

    }

    public function test_create()
    {
        $category = Category::create(['name' => 'test']);
        $category->refresh();

        $this->assertEquals('test', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create(['name' => 'test', 'description' => null]);
        $this->assertNull($category->description);

        $category = Category::create(['name' => 'test', 'description' => 'test_description']);
        $this->assertEquals('test_description', $category->description);

        $category = Category::create(['name' => 'test', 'is_active' => false]);
        $this->assertFalse($category->is_active);

        $category = Category::create(['name' => 'test', 'is_active' => true]);
        $this->assertTrue($category->is_active);
    }

    public function test_update()
    {
        $create = ['description' => 'test_description', 'is_active' => false];
        $values = ['name' => 'test_name_updated', 'description' => 'test_description_updated', 'is_active' => true];

        $category = Category::factory()->create($create)->first();

        $category->update($values);

        foreach ($values as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }

    }

}
