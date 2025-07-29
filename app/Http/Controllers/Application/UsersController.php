<?php

namespace App\Http\Controllers\Application;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Notifications\SendOtp;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Application\Auth\SignupRequest;
use App\Http\Requests\Application\Auth\VerifyEmailRequest;
use App\Http\Requests\Application\Auth\ResetPasswordRequest;
use App\Http\Requests\Application\Auth\ForgtePasswordRequest;

class UsersController extends Controller
{
    public function signUp(SignupRequest $request)
    {
        try{
            DB::beginTransaction();
            $userData = $request->validated();           
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
            ]);

            $user->notify(new SendOtp('email verification'));

            $token = $user->createToken('User Token')->plainTextToken;
            DB::commit();   
            return ApiResponse::success([
                'token' => $token,
                'User' => $user
            ], 'verification email sent successfully, please check your inbox.');

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('An error occurred while signing up. Please try again later.');
        }
    }

    public function login(LoginRequest $request)
    {
        try { 
            $credentials = $request->validated();

            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return ApiResponse::unauthorized('Your credentials does not match our records.');
            }

            $token = $user->createToken('User Token')->plainTextToken;
            if (is_null($user->email_verified_at)) {
                return ApiResponse::apiFormat(
                    [
                        'data' => [
                            'token' => $token,
                            'user' => $user
                        ]
                    ],
                    'Please verify your email before logging in.',
                    Response::HTTP_FORBIDDEN
                );
            }
            return ApiResponse::success([
                'token' => $token,
                'user' => $user
            ], 'Login successful');
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        // try{
            $data = $request->validated();
            $user = auth()->user();

            if (!is_null($user->email_verified_at)) {
                return ApiResponse::validationError([
                    'email' => 'Email is already verified'
                ]);
            }

            $otpCheck = (new Otp)->validate($email, $otp);
            if (!$otpCheck->status) {
                return ApiResponse::validationError([
                    'otp' => 'This OTP is invalid'
                ]);
            } 

            $user->email_verified_at = now();
            $user->save();
            DB::table('otps')->where('identifier', $request->email)->delete();

            return ApiResponse::message('Email verified successfully');
        // } catch (\Exception $e) {
        //     return ApiResponse::serverError('An error occurred while processing, please try again.');
        // }
    }

    public function resendOtp(Request $request)
    {
        try {
            $user = auth()->user();

            if (!is_null($user->email_verified_at)) {
                return ApiResponse::validationError([
                    'email' => 'Email is already verified'
                ]);
            }
            $user->notify(new SendOtp('email verification'));
            return ApiResponse::message('Verification email resent successfully');
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function forgotPassword(ForgtePasswordRequest $request)
    {
        try{
            $data = $request->validated();
            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                return ApiResponse::validationError([
                        'email' => 'No user found with this email address'
                ]);
            }

            $user->notify(new SendOtp('reset password'));

            return ApiResponse::message('Password reset instructions sent to your email.');
        }catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $data = $request->validated();
            $user = User::where('email', $data['email'])->first();
            
            if (!$user) {
                return ApiResponse::validationError([
                        'email' => 'No user found with this email address'
                ]);
            }
            
            $otpCheck = (new Otp)->validate($email, $otp);
            if (!$otpCheck->status) {
                return ApiResponse::validationError([
                    'otp' => 'This OTP is invalid'
                ]);
            }            
            $user->password = Hash::make($data['password']);
            $user->save();
            DB::table('otps')->where('identifier', $request->email)->delete();
            
            return ApiResponse::message('Password reset successfully');
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function logout(Request $requset)
    {
        if ($user = auth()->user()) {
            $user->tokens()->delete();
            return ApiResponse::message('Logout successful');
        }
        return ApiResponse::unauthorized('Unauthorized');
    }

    public function profile(Request $request)
    {
        // Logic to get user profile
    }
}
