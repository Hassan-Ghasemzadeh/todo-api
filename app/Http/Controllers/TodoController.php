<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
use App\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{

    use ApiResponder;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');
        $todos = $auth->user()->todos()->latest()->get();
        return $this->successResponse($todos, 'Fetched todos');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'is_completed' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }
        /** @var Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');
        $todo = $auth->user()->todos()->create($validator->validated());
        return $this->successResponse($todo, 'Todo created', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /** @var Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');
        $todo = $auth->user()->todos()->find($id);
        if (!$todo) {
            return $this->errorResponse('Todo not found', 404);
        }
        return $this->successResponse($todo, 'Todo Fetched');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        /** @var Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');
        $todo = $auth->user()->todos()->find($id);
        if (!$todo) {
            return $this->errorResponse('Todo not found', 404);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'is_completed' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        $todo->update($validator->validated());
        return $this->successResponse($todo, 'Todo updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /** @var Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');
        $todo = $auth->user()->todos()->find($id);
        if (!$todo) {
            return $this->errorResponse('Todo not found', 404);
        }
        $todo->delete();
        return $this->successResponse(null, 'Todo deleted');
    }
}
