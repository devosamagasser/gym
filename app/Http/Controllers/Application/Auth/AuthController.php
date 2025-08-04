<?php

namespace App\Http\Controllers\Application\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Notifications\SendOtp;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Requests\Auth\EmailVerificationRequest;

class AuthController extends Controller
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

            $token = $user->createToken('User Token',['not-verified'])->plainTextToken;
            DB::commit();   
            return ApiResponse::success([
                'token' => $token,
                'user' => $user
            ], 'verification email sent successfully, please check your inbox.');

        } catch (\Exception $e) {
            DB::rollBack();
            // return ApiResponse::serverError('An error occurred while signing up. Please try again later.');
            return ApiResponse::serverError($e->getMessage());
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

            
            if (is_null($user->email_verified_at)) {
                $token = $user->createToken('User Token',['not-verified'])->plainTextToken;
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
            } else {
                $token = $user->createToken('User Token',['verified'])->plainTextToken;
                return ApiResponse::success([
                    'token' => $token,
                    'user' => $user
                ], 'Login successful');
            }
            
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        try{
            $data = $request->validated();
            $user = auth()->user();

            if (!is_null($user->email_verified_at)) {
                return ApiResponse::validationError([
                    'email' => 'Email is already verified'
                ]);
            }

            $otpCheck = (new Otp)->validate($user->email, $data['otp']);
            if (!$otpCheck->status) {
                return ApiResponse::validationError([
                    'otp' => 'This OTP is invalid'
                ]);
            } 

            $user->email_verified_at = now();
            $user->save();
            DB::table('otps')->where('identifier', $user->email)->delete();

            $user->tokens()->delete();
            $token = $user->createToken('User Token',['verified'])->plainTextToken;
            return ApiResponse::success([
                'token' => $token,
                'user' => $user
            ], 'Email verified successfully');

        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function resendOtp(EmailVerificationRequest $request)
    {
        try {
            $data = $request->validated();
            $user = User::where('email', $data['email'])->first();

            $user->notify(new SendOtp('email verification'));
            return ApiResponse::message('Verification email resent successfully');
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function logout(Request $request)
    {
        if ($user = auth()->user()) {
            $user->tokens()->delete();
            return ApiResponse::message('Logout successful');
        }
        return ApiResponse::unauthorized('Unauthorized');
    }

}
