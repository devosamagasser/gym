<?php

namespace App\Http\Controllers\Application\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Facades\ApiResponse;
use App\Notifications\SendOtp;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\EmailVerificationRequest;
use App\Http\Requests\Auth\ResetPasswordVerificationRequest;

class ResetPasswordController extends Controller
{

    public function forgotPassword(EmailVerificationRequest $request)
    {
        try{
            $data = $request->validated();
            $user = User::where('email', $data['email'])->first();

            $user->notify(new SendOtp('reset password'));

            return ApiResponse::message('Password reset code sent to your email.');
        }catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function resetPasswordVerification(ResetPasswordVerificationRequest $request)
    {
        try {
            $data = $request->validated();
            $user = User::where('email', $data['email'])->first();
        
            $otpCheck = (new Otp)->validate($data['email'], $data['otp']);
            if (!$otpCheck->status) {
                return ApiResponse::validationError([
                    'otp' => 'This OTP is invalid'
                ]);
            }        
            DB::table('otps')->where('identifier', $request->email)->delete();

            $token = $user->createToken('password-reset-token', ['reset-password'])->plainTextToken;

            return ApiResponse::success(
                ['token' => $token,], 
                'OTP verified successfully, you can now reset your password.'
            );

        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = auth()->user();
            $data = $request->validated();

            $user->update([
                'password' => Hash::make($data['password']),
            ]);
            $user->currentAccessToken()->delete();
            return ApiResponse::message('Password reset successfully');
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

}
