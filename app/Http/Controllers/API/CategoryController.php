<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    private $rules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean',
    ];

    public function index(): JsonResponse
    {
        return response()->json(Category::all());
    }

    public function store(Request $request): JsonResponse
    {
        $serialized = $request->only(array_keys($this->rules));

        $this->validate($request, $this->rules);
        $category = Category::create($serialized);
        $category->refresh();

        return response()->json($category, 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $serialized = $request->only(array_keys($this->rules));

        $this->validate($request, $this->rules);

        $category->update($serialized);

        return response()->json($category);

    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([], 204);

    }
}
