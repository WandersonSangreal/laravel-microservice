<?php

namespace App\Http\Controllers\API;

use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class GenderController extends Controller
{
    /**
     * @var string[]
     */
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Gender::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $serialized = $request->only(array_keys($this->rules));

        $this->validate($request, $this->rules);
        $gender = Gender::create($serialized);
        $gender->refresh();

        return response()->json($gender, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Gender $gender
     * @return JsonResponse
     */
    public function show(Gender $gender): JsonResponse
    {
        return response()->json($gender);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Gender $gender
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, Gender $gender): JsonResponse
    {
        $serialized = $request->only(array_keys($this->rules));

        $this->validate($request, $this->rules);

        $gender->update($serialized);

        return response()->json($gender);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Gender $gender
     * @return JsonResponse
     */
    public function destroy(Gender $gender): JsonResponse
    {
        $gender->delete();

        return response()->json([], 204);
    }
}
