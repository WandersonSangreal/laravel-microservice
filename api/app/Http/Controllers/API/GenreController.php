<?php

namespace App\Http\Controllers\API;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\GenreResource;

class GenreController extends ResourceAbstractController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
        ];
    }

    protected function model(): string
    {
        return Genre::class;
    }

    protected function resource(): string
    {
        return GenreResource::class;
    }

    protected function enablePagination(): bool
    {
        return true;
    }

    protected function rulesStore(): array
    {
        return $this->rules;
    }

    protected function rulesUpdate(): array
    {
        return $this->rules;
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, $this->rulesStore());

        $item = DB::transaction(function () use ($validated, $request) {
            $item = $this->model()::create($validated);
            self::handleRelations($item, $request);
            return $item;
        });

        $item->refresh();
        $resource = $this->resource();

        return new $resource($item);

    }

    public function update(Request $request, $id)
    {
        $values = $this->validate($request, $this->rulesStore());

        $item = $this->findOrFail($id);

        $item = DB::transaction(function () use ($item, $values, $request) {
            $item->update($values);
            self::handleRelations($item, $request);
            return $item;
        });

        $item->refresh();
        $resource = $this->resource();

        return new $resource($item);
    }

    protected function handleRelations($item, Request $request)
    {
        $item->categories()->sync($request->get('categories_id'));
    }

}
