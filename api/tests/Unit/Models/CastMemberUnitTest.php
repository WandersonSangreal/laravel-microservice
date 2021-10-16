<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CastMemberUnitTest extends TestCase
{
    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();

        $this->castMember = new CastMember();
    }


    public function test_fillable_attribute()
    {
        $fillable = ['name', 'type'];

        $castMemberFillable = $this->castMember->getFillable();

        array_multisort($fillable, $castMemberFillable);

        $this->assertEquals($fillable, $castMemberFillable);
    }

    public function test_if_use_traits()
    {
        $traits = [Uuid::class, HasFactory::class, SoftDeletes::class];

        $castMemberTraists = array_keys(class_uses(CastMember::class));

        array_multisort($traits, $castMemberTraists);

        $this->assertEquals($traits, $castMemberTraists);
    }

    public function test_casts_attribute()
    {
        $casts = ['id' => 'string', 'deleted_at' => 'datetime'];

        $castMemberCasts = $this->castMember->getCasts();

        array_multisort($casts, $castMemberCasts);

        $this->assertEquals($casts, $castMemberCasts);
    }

    public function test_dates_attribute()
    {
        $dates = ['created_at', 'updated_at'];

        $castMemberDates = $this->castMember->getDates();

        array_multisort($dates, $castMemberDates);

        $this->assertEquals($dates, $castMemberDates);
    }

    public function test_incrementing()
    {
        $this->assertFalse($this->castMember->incrementing);
    }

}
