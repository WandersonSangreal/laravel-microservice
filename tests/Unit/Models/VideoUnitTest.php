<?php

namespace Tests\Unit\Models;

use App\Models\Video;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class VideoUnitTest extends TestCase
{
    private $video;

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = new Video();
    }

    public function test_fillable_attribute()
    {
        $fillable = ['title', 'description', 'year_launched', 'opened', 'rating', 'duration'];

        $videoFillable = $this->video->getFillable();

        array_multisort($fillable, $videoFillable);

        $this->assertEquals($fillable, $videoFillable);
    }

    public function test_if_use_traits()
    {
        $traits = [Uuid::class, HasFactory::class, SoftDeletes::class];

        $videoTraists = array_keys(class_uses(Video::class));

        array_multisort($traits, $videoTraists);

        $this->assertEquals($traits, $videoTraists);
    }

    public function test_casts_attribute()
    {
        $casts = ['id' => 'string', 'deleted_at' => 'datetime', 'opened' => 'boolean', 'year_launched' => 'integer', 'duration' => 'integer'];

        $videoCasts = $this->video->getCasts();

        array_multisort($casts, $videoCasts);

        $this->assertEquals($casts, $videoCasts);
    }

    public function test_dates_attribute()
    {
        $dates = ['created_at', 'updated_at'];

        $videoDates = $this->video->getDates();

        array_multisort($dates, $videoDates);

        $this->assertEquals($dates, $videoDates);
    }

    public function test_incrementing()
    {
        $this->assertFalse($this->video->incrementing);
    }
}
