<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = new Category();
    }


    public function test_fillable_attribute()
    {
        $fillable = ['name', 'description', 'is_active'];

        $categoryFillable = $this->category->getFillable();

        array_multisort($fillable, $categoryFillable);

        $this->assertEquals($fillable, $categoryFillable);
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
        $casts = ['id' => 'string', 'deleted_at' => 'datetime', 'is_active' => 'boolean'];

        $categoryCasts = $this->category->getCasts();

        array_multisort($casts, $categoryCasts);

        $this->assertEquals($casts, $categoryCasts);
    }

    public function test_dates_attribute()
    {
        $dates = ['created_at', 'updated_at'];

        $categoryDates = $this->category->getDates();

        array_multisort($dates, $categoryDates);

        $this->assertEquals($dates, $categoryDates);
    }

    public function test_incrementing()
    {
        $this->assertFalse($this->category->incrementing);
    }

}
