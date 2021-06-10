<?php

namespace App\Http\Controllers\API;

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

        $value = $this->model()::create($validated);
        $value->refresh();
        return $value;
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    /*

    public function show(Category $category): JsonResponse
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $serialized = $request->only(array_keys($this->rules));

        $this->validate($request, $this->rules);

        $category->update($serialized);

        return response()->json($category);

    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([], 204);

    }
    */
}
