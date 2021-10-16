<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;

class CastMemberController extends ResourceAbstractController
{
    private $types;

    public function __construct()
    {
        $this->types = implode(',', [CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR]);
    }

    protected function model(): string
    {
        return CastMember::class;
    }

    protected function resource(): string
    {
        return CastMemberResource::class;
    }

    protected function enablePagination(): bool
    {
        return true;
    }

    protected function rulesStore(): array
    {
        return [
            'name' => 'required|max:255',
            'type' => "required|numeric|between:$this->types"
        ];
    }

    protected function rulesUpdate(): array
    {
        return [
            'name' => 'required|max:255',
            'type' => "required|numeric|between:$this->types"
        ];
    }
}
