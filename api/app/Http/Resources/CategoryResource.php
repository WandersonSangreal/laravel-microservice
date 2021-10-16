<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class CategoryResource extends JsonResource
{
    # public static $wrap = false;

    public function toArray($request): array
    {
        return parent::toArray($request);

        # return [
        # 'id' => $this->id,
        # 'name' => $this->name
        # ];

    }
}
