<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;
use ReflectionClass;

abstract class ResourceAbstractController extends Controller
{
    private $paginationSize = 15;

    protected abstract function enablePagination();

    protected abstract function model();

    protected abstract function resource();

    protected abstract function rulesStore();

    protected abstract function rulesUpdate();

    public function index()
    {
        $items = $this->enablePagination() ? $this->model()::paginate($this->paginationSize) : $this->model()::all();

        $resourceCollection = $this->resource();
        $reflectionClass = new ReflectionClass($resourceCollection);

        if ($reflectionClass->isSubclassOf(ResourceCollection::class)) {
            return new $resourceCollection($items);
        }

        return $resourceCollection::collection($items);
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, $this->rulesStore());

        $item = $this->model()::create($validated);
        $item->refresh();

        $resource = $this->resource();

        return new $resource($item);
    }

    public function show($id)
    {
        $item = $this->findOrFail($id);

        $resource = $this->resource();

        return new $resource($item);

    }

    public function update(Request $request, $id)
    {
        $values = $this->validate($request, $this->rulesStore());

        $item = $this->findOrFail($id);
        $item->update($values);

        # $item->refresh();

        $resource = $this->resource();

        return new $resource($item);
    }

    public function destroy($id): JsonResponse
    {
        $item = $this->findOrFail($id);

        $item->delete();

        return response()->json([], 204);

    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

}
