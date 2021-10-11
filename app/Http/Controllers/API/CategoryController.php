<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use JetBrains\PhpStorm\ArrayShape;

class CategoryController extends ResourceAbstractController
{
    private array $rules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean',
    ];

    protected function model(): string
    {
        return Category::class;
    }

    #[ArrayShape(['name' => "string", 'description' => "string", 'is_active' => "string"])]
    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ];
    }

    #[ArrayShape(['name' => "string", 'description' => "string", 'is_active' => "string"])]
    protected function rulesUpdate(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ];
    }
}
