<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'message' => 'Email / Password salah'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'user' => auth()->user(),
            'token' => $token
        ]);
    }

    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => 2, // role pengguna
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Register berhasil',
        'user' => $user,
    ], 201);
}

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
