<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log('تم تسجيل حساب جديد');

        return ApiResponseHelper::success(['token' => $token], 'تم تسجيل الحساب بنجاح');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials']);
        }

        $token = $user->createToken($user->name . 'auth_token')->plainTextToken;

        return ApiResponseHelper::success(['token' => $token], 'تم تسجيل الدخول بنجاح');
    }

    public function verifyToken(Request $request)
    {
        try {
            // Use Laravel's built-in auth to authenticate the user
            $user = auth()->user();

            if (!$user) {
                return ApiResponseHelper::error('Invalid or expired token', 401);
            }

            return ApiResponseHelper::success(null, 'Token is valid');
        } catch (\Exception $e) {
            // Catch any exception thrown if the token is invalid or expired
            return ApiResponseHelper::error('Invalid or expired token', 401);
        }
    }

    public function refreshToken(Request $request)
    {
        // Delete the current token
        $request->user()->currentAccessToken()->delete();

        // Create a new token
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponseHelper::success(null, 'تم تسجيل الخروج بنجاح');
    }
}
