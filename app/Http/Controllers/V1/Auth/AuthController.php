<?php
namespace App\Http\Controllers\V1\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Enums\ErrorCode;

class AuthController extends Controller
{
     /**
     * Allows users to authenticate
     *
     * @param LoginRequest $request
     * @return void
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Attempt to authenticate the user
        $token = auth()->attempt($credentials);
        if(!$token) {
            // Authentication failed
            return response()->json(['message' => 'Invalid credentials'], ErrorCode::UNAUTHORIZED->value);
        }

        // Return a JSON response with the token
        return response()->json([
                'token' => auth()->user()->accessToken,
                'type' => 'bearer',
                'expires_in' => auth()->user()->expires_in,
            ],
            ErrorCode::OK->value
        );
    }
}
