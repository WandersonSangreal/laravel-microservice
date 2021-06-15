<?php

namespace App\Http\Controllers\API;

use App\Models\Gender;

class GenderController extends ResourceAbstractController
{

    protected function model(): string
    {
        return Gender::class;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
        ];
    }

    protected function rulesUpdate(): array
    {
        return [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
        ];
    }
}
