<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request
        $request->validate([
            'name'     => 'required|string|max:255|min:3',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'is_admin' => 'boolean'
        ]);
        // Create a new user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->is_admin
        ]);
        // Check if user was created
        if ($user) {
            return response()->json([
                'status'  => true,
                'message' => 'User created successfully',
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'User creation failed'
            ], 400);
        }
    }


    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);
        // Attempt to log the user in with the provided credentials and generate a JWTtoken
        $token = JWTAuth::attempt([
            "email"    => $request->email,
            "password" => $request->password
        ]);

        // Check if user was logged in
        if (!empty($token)) {
            return response()->json([
                'status'  => true,
                'message' => 'User Logged in successfully',
                'token'   =>  $token
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials'
            ], 401);
        }
    }

    public function profile(Request $request)
    {
        // Get the authenticated user
        $userDate = auth()->user();

        return response()->json([
            'status'  => true,
            'message' => 'Profile Data',
            'user'    => $userDate
        ], 200);
    }

    public function refreshToken(Request $request)
    {
        // Generate a new JWT token
        $newToken = auth()->refresh();

        return response()->json([
            'status'  => true,
            'message' => 'New access token generated successfully. Refreshed token.',
            'token'   => $newToken
        ], 200);
    }

    public function logout(Request $request)
    {
        // Logout the user and invalidate/destroy the token
        auth()->logout();

        return response()->json([
            'status'  => true,
            'message' => 'User logged out successfully'
        ], 200);
    }
}