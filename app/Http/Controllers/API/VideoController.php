<?php

namespace App\Http\Controllers\API;

use App\Models\Video;
use App\Rules\GenresHasCategoriesRule;
use Illuminate\Http\Request;

class VideoController extends ResourceAbstractController
{
    private array $rules;

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
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
            'genres_id' => [
                'required',
                'array',
                'exists:genres,id,deleted_at,NULL'
            ],
            'video_file' => 'nullable|mimes:mp4|max:' . Video::VIDEO_FILE_MAX_SIZE,
            'thumb_file' => 'nullable|mimes:jpg,jpeg,png|max:' . Video::THUMB_FILE_MAX_SIZE,
            'banner_file' => 'nullable|mimes:jpg,jpeg,png|max:' . Video::BANNER_FILE_MAX_SIZE,
            'trailer_file' => 'nullable|mimes:mp4|max:' . Video::TRAILER_FILE_MAX_SIZE,
        ];
    }

    public function store(Request $request)
    {
        $this->genreHasCategoriesRule($request);

        $validated = $this->validate($request, $this->rulesStore());

        $item = $this->model()::create($validated);

        $item->refresh();
        return $item;
    }

    public function update(Request $request, $id)
    {
        $this->genreHasCategoriesRule($request);

        $values = $this->validate($request, $this->rulesStore());

        $item = $this->findOrFail($id);

        $item->update($values);

        return $item;
    }

    protected function genreHasCategoriesRule(Request $request)
    {
        $values = is_array($request->get('categories_id')) ? $request->get('categories_id') : [];
        $this->rules['genres_id'][] = new GenresHasCategoriesRule($values);
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
