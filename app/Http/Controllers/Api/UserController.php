<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->is_admin) {
            $users = User::all();
            return response()->json([
                'status' => true,
                'data' => $users
            ], 200);
        } else {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if ($request->user()->id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
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
        $request->validate([
            'email' => 'email',
            'password' => 'min:6|confirmed'
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($request->user()->id == $user->id) {
            $user->email = $request->email ?? $user->email;
            $user->password = $request->has('password') ? Hash::make($request->password) : $user->password;
            $user->save();

            return response()->json(['status' => true, 'message' => 'User updated successfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($request->user()->is_admin || $request->user()->id == $user->id) {
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User deleted successfully'], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}