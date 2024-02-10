<?php
namespace App\Http\Controllers\V1\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Enums\ErrorCode;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

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
        $auth = auth()->attempt($credentials);
        if(!$auth) {
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

    public function register(RegisterRequest $request)
    {
        // Validate request data
        $data = $request->validated();

        // Check if user email already exists
        $user = User::where('email', $data['email'])
            ->first();
        if ($user) {
            return response()->json(['message' => 'User already exists.'], ErrorCode::BAD_REQUEST->value);
        }

        $user = User::create($data);
        // Save the user into the database
        if (!$user) {
            return response()->json(['message' => 'User registration failed.'], ErrorCode::BAD_REQUEST->value);
        }

        // attempt to authenticate
        $auth = auth()->attempt([
            "email" => $data["email"],
            "password" => $data["password"]
        ]);
        if(!$auth) {
            // Authentication failed
            return response()->json(['message' => 'Invalid credentials'], ErrorCode::UNAUTHORIZED->value);
        }

        // Return success response
        return response()->json([
                'message' => 'User created successfully!',
                'token' => auth()->user()->accessToken,
                'type' => 'bearer',
                'expires_in' => auth()->user()->expires_in,
            ],
            ErrorCode::CREATED->value
        );
    }
}
