<?php

namespace App\Http\Controllers\API;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends ResourceAbstractController
{
    private $rules;

    public function __construct()
    {
        $rating = implode(',', Video::RATING_LIST);

        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => "required|in:$rating",
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id',
            'genres_id' => 'required|array|exists:genres,id'
        ];
    }

    public function store(Request $request)
    {
        $validated = $this->validate($request, $this->rulesStore());

        $item = $this->model()::create($validated);
        $item->categories()->sync($request->get('categories_id'));
        $item->genres()->sync($request->get('genres_id'));
        $item->refresh();
        return $item;
    }

    public function update(Request $request, $id)
    {
        $values = $this->validate($request, $this->rulesStore());

        $item = $this->findOrFail($id);
        $item->update($values);

        $item->categories()->sync($request->get('categories_id'));
        $item->genres()->sync($request->get('genres_id'));

        return $item;
    }

    protected function model(): string
    {
        return Video::class;
    }

    protected function rulesStore(): array
    {
        return $this->rules;
    }

    protected function rulesUpdate(): array
    {
        return $this->rules;
    }
}
