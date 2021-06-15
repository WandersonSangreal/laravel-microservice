<?php

namespace Tests\Unit\Models;

use App\Models\Gender;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class GenderUnitTest extends TestCase
{
    private $gender;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gender = new Gender();
    }

    public function test_fillable_attribute()
    {
        $fillable = ['name', 'is_active'];

        $genderFillable = $this->gender->getFillable();

        array_multisort($fillable, $genderFillable);

        $this->assertEquals($fillable, $genderFillable);
    }

    public function test_if_use_traits()
    {
        $traits = [Uuid::class, HasFactory::class, SoftDeletes::class];

        $genderTraists = array_keys(class_uses(Gender::class));

        array_multisort($traits, $genderTraists);

        $this->assertEquals($traits, $genderTraists);
    }

    public function test_casts_attribute()
    {
        $casts = ['id' => 'string', 'deleted_at' => 'datetime', 'is_active' => 'boolean'];

        $genderCasts = $this->gender->getCasts();

        array_multisort($casts, $genderCasts);

        $this->assertEquals($casts, $genderCasts);
    }

    public function test_dates_attribute()
    {
        $dates = ['created_at', 'updated_at'];

        $genderDates = $this->gender->getDates();

        array_multisort($dates, $genderDates);

        $this->assertEquals($dates, $genderDates);
    }

    public function test_incrementing()
    {
        $this->assertFalse($this->gender->incrementing);
    }
}
