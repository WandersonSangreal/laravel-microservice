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
        ];
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
