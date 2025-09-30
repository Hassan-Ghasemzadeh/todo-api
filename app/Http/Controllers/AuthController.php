<?php

namespace App\Http\Controllers;

use App\ApiResponder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponder;

    /**
     * login,token generation JWT.
     * * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation error',
                422,
                $validator->errors(),
            );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');

        $token = $auth->login($user);

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], 'User registred', 201);
    }

    function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');

        if (!$token = $auth->attempt($credentials)) {
            return $this->errorResponse('Invalid credentials', 401);
        }
        return $this->successResponse([
            'token' => $token
        ], 'Logged in');
    }

    function me()
    {
        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');
        return $this->successResponse([$auth->user()]);
    }

    function logout()
    {
        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');
        $auth->logout();

        return $this->successResponse(null, 'Logged out');
    }

    function refresh()
    {
        /** @var \Tymon\JWTAuth\JWTGuard $auth */
        $auth = auth('api');

        $newToken =  $auth->refresh();

        return $this->successResponse([
            'token' => $newToken,
        ], 'Token refreshed');
    }
}
