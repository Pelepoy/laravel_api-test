<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if the user is an admin
        if ((int) auth()->user()->is_admin === 1) {

            /// Get all users
            $users = User::all();
            return response()->json([
                'status' => true,
                'data' => $users
            ], 200);
        } elseif ((int) auth()->user()->is_admin === 0) {
            return response()->json([
                'error' => 'You are not authorized to view this resource. Not an admin/authorized user'
            ], 401);
        }
        return response()->json([
            'error' => 'Unauthorized'
        ], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        // Get the user
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }
        // Check if the user is the owner of the user details
        if ($request->user()->id != $user->id) {
            return response()->json([
                'error' => 'Unauthorized. You do not own this details'
            ], 401);
        }
        return response()->json([
            'status' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Get the user
        $user = User::findOrFail($id);

        // Validate the request
        $validated = $request->validate([
            'name'     => 'string|max:255|min:3',
            'email'    => 'email|unique:users,email,' . $user->id,
            'password' => 'string|min:6|confirmed',
        ]);

        // Check if the user exists
        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        // Check if the user is authorized to update the details
        if ($request->user()->id == $user->id) {
            // Hash the password if it's present in the request
            if ($request->has('password')) {
                $validated['password'] = Hash::make($validated['password']);
            }
            // Update the user details
            $user->update($validated);

            // Generate a new JWT for the user
            $newToken = JWTAuth::fromUser($user);
            return response()->json([
                'success' => true,
                'message' => 'User details updated successfully',
                'token' => $newToken,
                'student' => $user
            ], 200);
        } else {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        // Get the user
        $user = User::find($id);
        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        // Check if the user is an admin or the owner of the user details
        if ($request->user()->is_admin || $request->user()->id == $user->id) {
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User deleted successfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}