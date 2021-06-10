<?php

namespace App\Http\Controllers\API;

use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class GenderController extends Controller
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
    ];

    public function index(): JsonResponse
    {
        return response()->json(Gender::all());
    }

    public function store(Request $request): JsonResponse
    {
        $serialized = $request->only(array_keys($this->rules));

        $this->validate($request, $this->rules);
        $gender = Gender::create($serialized);
        $gender->refresh();

        return response()->json($gender, 201);
    }

    public function show(Gender $gender): JsonResponse
    {
        return response()->json($gender);
    }

    public function update(Request $request, Gender $gender): JsonResponse
    {
        $serialized = $request->only(array_keys($this->rules));

        $this->validate($request, $this->rules);

        $gender->update($serialized);

        return response()->json($gender);
    }

    public function destroy(Gender $gender): JsonResponse
    {
        $gender->delete();

        return response()->json([], 204);
    }
}
