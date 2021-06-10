<?php


namespace Tests\Feature\Http\Controllers\API;


use App\Http\Controllers\API\ResourceAbstractController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Mockery;
use ReflectionClass;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;

class ResourceAbstractControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function test_index()
    {
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);

        $this->assertEquals([$category->toArray()], $this->controller->index()->toArray());
    }

    public function test_invalidation_data_store()
    {
        $this->expectException(ValidationException::class);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => '']);

        $this->controller->store($request);
    }

    public function test_store()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => 'test_namme', 'description' => 'test_description']);

        $value = $this->controller->store($request);

        $this->assertEquals(CategoryStub::find(1)->toArray(), $value->toArray());
    }

    public function test_if_find_or_fail_fetch_model()
    {
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);

        $reflectionClass = new ReflectionClass(ResourceAbstractController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $value = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $value);
    }

    public function test_if_find_or_fail_throw_exception_when_id_invalid()
    {
        $this->expectException(ModelNotFoundException::class);

        $reflectionClass = new ReflectionClass(ResourceAbstractController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $value = $reflectionMethod->invokeArgs($this->controller, [0]);
        $this->assertInstanceOf(CategoryStub::class, $value);
    }
}
