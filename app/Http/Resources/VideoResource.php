<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $complements = [
            'categories' => CategoryResource::collection($this->categories),
            'genres' => GenreResource::collection($this->genres)
        ];

        return parent::toArray($request) + $complements;
    }
}
