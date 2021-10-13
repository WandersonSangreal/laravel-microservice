<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GenreResource extends JsonResource
{
    public function toArray($request)
    {
        $complements = ['categories' => CategoryResource::collection($this->categories)];

        return parent::toArray($request) + $complements;
    }
}
