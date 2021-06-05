<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{

    use DatabaseMigrations;

    public function test_fillable_attribute()
    {
        $fillable = ['name', 'description', 'is_active'];

        $category = new Category();

        $this->assertEquals($fillable, $category->getFillable());
    }

    public function test_if_use_traits()
    {
        $traits = [Uuid::class, HasFactory::class, SoftDeletes::class];

        $categoryTraists = array_keys(class_uses(Category::class));

        array_multisort($traits, $categoryTraists);

        $this->assertEquals($traits, $categoryTraists);
    }

    public function test_casts_attribute()
    {
        $casts = ['id' => 'string', 'deleted_at' => 'datetime'];

        $category = new Category();

        $this->assertEquals($casts, $category->getCasts());
    }

    public function test_dates_attribute()
    {
        $dates = ['created_at', 'updated_at'];

        $category = new Category();
        $categoryDates = $category->getDates();

        array_multisort($dates, $categoryDates);

        $this->assertEquals($dates, $categoryDates);
    }

    public function test_incrementing()
    {
        $category = new Category();

        $this->assertFalse($category->incrementing);
    }

}
