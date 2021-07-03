<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $castMember;
    private $sendValue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->castMember = Video::factory()->create();
        $this->sendValue = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
        ];
    }

    public function test_index()
    {
        $response = $this->get(route('videos.index'));

        $response->assertStatus(200)->assertJson([$this->castMember->toArray()]);
    }

    public function test_show()
    {
        $response = $this->get(route('videos.show', ['video' => $this->castMember->id]));

        $response->assertStatus(200)->assertJson($this->castMember->toArray());
    }

    public function test_invalidation_required()
    {

        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];

        $this->assertInvalidationStoreAction($data, 'required');
        $this->assertInvalidationUpdateAction($data, 'required');

    }

    public function test_invalidation_max()
    {

        $data = ['title' => str_repeat('a', 256)];

        $this->assertInvalidationStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationUpdateAction($data, 'max.string', ['max' => 255]);

    }

    public function test_invalidation_integer()
    {

        $data = ['duration' => 's'];

        $this->assertInvalidationStoreAction($data, 'integer');
        $this->assertInvalidationUpdateAction($data, 'integer');

    }

    public function test_invalidation_year_launched_field()
    {

        $data = ['year_launched' => 'a'];

        $this->assertInvalidationStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationUpdateAction($data, 'date_format', ['format' => 'Y']);

    }

    public function test_invalidation_opened_field()
    {

        $data = ['opened' => 's'];

        $this->assertInvalidationStoreAction($data, 'boolean');
        $this->assertInvalidationUpdateAction($data, 'boolean');

    }

    public function test_invalidation_rating_field()
    {

        $data = ['rating' => 0];

        $this->assertInvalidationStoreAction($data, 'in');
        $this->assertInvalidationUpdateAction($data, 'in');

    }

    public function test_invalidation_categories_field()
    {

        $data = ['categories_id' => 'a'];

        $this->assertInvalidationStoreAction($data, 'array');
        $this->assertInvalidationUpdateAction($data, 'array');

        $data = ['categories_id' => [100]];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

    }

    public function test_invalidation_genres_field()
    {

        $data = ['genres_id' => 'a'];

        $this->assertInvalidationStoreAction($data, 'array');
        $this->assertInvalidationUpdateAction($data, 'array');

        $data = ['genres_id' => [100]];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exists');

    }

    public function test_save()
    {

        $data = [
            [
                'send_data' => $this->sendValue,
                'test_data' => $this->sendValue + ['opened' => false]
            ],
            [
                'send_data' => $this->sendValue + ['opened' => true],
                'test_data' => $this->sendValue + ['opened' => true]
            ],
            [
                'send_data' => $this->sendValue + ['rating' => Video::RATING_LIST[2]],
                'test_data' => $this->sendValue + ['rating' => Video::RATING_LIST[2]]
            ],
        ];

        foreach ($data as $value) {

            $response = $this->assertStore($value['send_data'], $value['test_data']);

            $response->assertJsonStructure(['created_at', 'updated_at']);

            $response = $this->assertStore($value['send_data'], $value['test_data']);

            $response->assertJsonStructure(['created_at', 'updated_at']);

        }

    }

    public function test_destroy()
    {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->castMember->id]));

        $response->assertStatus(204);

        $this->assertSoftDeleted($this->castMember);
    }

    protected function routeStore(): string
    {
        return route('videos.store');
    }

    protected function routeUpdate(): string
    {
        return route('videos.update', ['video' => $this->castMember->id]);
    }

    protected function model(): string
    {
        return Video::class;
    }
}
