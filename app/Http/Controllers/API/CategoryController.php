<?php

namespace App\Http\Controllers\API;

use App\Models\Category;

class CategoryController extends ResourceAbstractController
{
    private $rules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean',
    ];

    protected function model(): string
    {
        return Category::class;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ];
    }

    protected function rulesUpdate(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ];
    }
}
