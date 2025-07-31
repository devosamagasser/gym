<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Admin;
use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try { 
            $credentials = $request->validated();

            $admin = Admin::where('email', $credentials['email'])->first();

            if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
                return ApiResponse::unauthorized('Your credentials do not match our records.');
            }

            $token = $admin->createToken('Admin Token')->plainTextToken;

            return ApiResponse::success([
                'token' => $token,
                'admin' => $admin
            ], 'Login successful');
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function logout(Request $request)
    {
        try {
            $admin = auth('admins')->user();   

            if ($admin) {
                $admin->tokens()->delete();
                return ApiResponse::message('Logout successful');
            }
            return ApiResponse::unauthorized('Unauthorized');
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

}
