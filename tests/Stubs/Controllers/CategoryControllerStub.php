<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\API\ResourceAbstractController;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends ResourceAbstractController
{

    protected function model(): string
    {
        return CategoryStub::class;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }

    protected function rulesUpdate(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }
}
