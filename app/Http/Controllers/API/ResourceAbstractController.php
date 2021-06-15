<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;

abstract class ResourceAbstractController extends Controller
{
    protected abstract function model();

    protected abstract function rulesStore();

    protected abstract function rulesUpdate();

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, $this->rulesStore());

        $item = $this->model()::create($validated);
        $item->refresh();
        return $item;
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id)
    {
        return $this->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $values = $this->validate($request, $this->rulesStore());

        $item = $this->findOrFail($id);
        $item->update($values);

        $item->refresh();

        return $item;
    }

    public function destroy($id): JsonResponse
    {
        $item = $this->findOrFail($id);

        $item->delete();

        return response()->json([], 204);

    }
}
