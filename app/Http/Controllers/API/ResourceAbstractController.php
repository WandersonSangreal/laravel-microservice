<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;

abstract class ResourceAbstractController extends Controller
{
    protected abstract function model();

    protected abstract function rulesStore();

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, $this->rulesStore());

        $category = $this->model()::create($validated);
        $category->refresh();
        return $category;
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id): JsonResponse
    {
        $category = $this->findOrFail($id);

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $values = $this->validate($request, $this->rulesStore());

        $category = $this->findOrFail($id);
        $category->update($values);

        $category->refresh();

        return $category;
    }

    public function destroy($id): JsonResponse
    {
        $category = $this->findOrFail($id);

        $category->delete();

        return response()->json([], 204);

    }
}
