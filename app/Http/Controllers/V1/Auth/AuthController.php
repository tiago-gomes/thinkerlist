<?php
namespace App\Http\Controllers\V1\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Enums\ErrorCode;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Carbon\Carbon;


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
        $auth = Auth::guard('web')->attempt($credentials);
        if(!$auth) {
            // Authentication failed
            return response()->json(['message' => 'Invalid credentials'], ErrorCode::UNAUTHORIZED->value);
        }

        // generate sacntum token
        $token = $request->user()->createToken('token', ['*'], now()->addMinutes(60));

        // Return a JSON response with the token
        return response()->json([
                'token' => $token->plainTextToken,
                'type' => 'bearer',
                'expires_in' => $token->accessToken->expires_at,
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

        // Save the user into the database
        $user = User::create($data);
        if (!$user) {
            return response()->json(['message' => 'User registration failed.'], ErrorCode::BAD_REQUEST->value);
        }

        // attempt to authenticate
        $auth = Auth::guard('web')->attempt([
            "email" => $data["email"],
            "password" => $data["password"]
        ]);
        if(!$auth) {
            // Authentication failed
            return response()->json(['message' => 'Invalid credentials'], ErrorCode::UNAUTHORIZED->value);
        }

        // generate sacntum token
        $token = $request->user()->createToken('token', ['*'], now()->addMinutes(60));

        // Return success response
        return response()->json([
                'message' => 'User created successfully!',
                'token' => $token->accessToken->token,
                'type' => 'bearer',
                'expires_in' => $token->accessToken->expires_at,
            ],
            ErrorCode::CREATED->value
        );
    }

    public function logout()
    {
        try {
            // Revoke the current user's token
            auth()->user()->tokens()->delete();
            return response()->json(['message' => 'Logout successful'], ErrorCode::OK->value);
        } catch(Exception $e) {
            return response()->json(['message' => 'Unable to complete logout. Please try again later.'], ErrorCode::BAD_REQUEST->value);
        }
    }
}
