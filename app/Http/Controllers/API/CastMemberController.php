<?php

namespace App\Http\Controllers\API;

use App\Models\CastMember;

class CastMemberController extends ResourceAbstractController
{
    protected function model(): string
    {
        return CastMember::class;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'type' => 'required:min:1|max:2'
        ];
    }

    public function index()
    {
        return parent::index();
    }
}
