<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Libraries\AppLibrary;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\Auth\SendResetPassword;
use Smartisan\Settings\Facades\Settings;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\RegisterWithEmailAndPasswordRequest;

class AuthService
{

    /**
     * @throws Exception
     */
    public function registerWithEmailAndPassword(RegisterWithEmailAndPasswordRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($request->password);

            if ($request->username) {
                $data['username'] = $request->username;
            } else {
                $data['username'] = AppLibrary::generateUsername($request->name);
            }
            return UserModel::create($data);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function checkAvailabilityUsername($username)
    {
        try {
            $user = UserModel::where('username', $username)->first();
            if ($user) {
                return [
                    'availability'     => false
                ];
            } else {
                return [
                    'availability'     => true
                ];
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }


    /**
     * @throws Exception
     */
    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->validated())) {
                return UserModel::where('email', $request->email)->first();
            } else {
                throw new Exception(trans('auth.invalid_credentials'));
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function sendResetPasswordLink(string $email)
    {
        try {
            $pin            = null;
            $userIsExists   = UserModel::where('email', $email)->exists();

            if ($userIsExists) {
                $passwordReset = DB::table('password_reset_tokens')->where([
                    ['email', $email],
                    ['isActive', 1],
                ]);

                if ($passwordReset->exists()) {
                    $passwordReset->delete();
                }

                $pin = rand(
                    pow(10, (int)Settings::group('otp')->get('otp_digit_limit') - 1),
                    pow(10, (int)Settings::group('otp')->get('otp_digit_limit')) - 1
                );

                $passwordReset = DB::table('password_reset_tokens')->insert([
                    'email'       => $email,
                    'token'       => $pin,
                    'isActive'    => 1,
                    'created_at'  => Carbon::now()
                ]);
                if ($passwordReset) {
                    SendResetPassword::dispatch(['email' => $email, 'code' => $pin]);
                    return trans('auth.check_your_email_for_code');
                } else {
                    throw new Exception(trans('auth.token_created_fail'));
                }
            } else {
                throw new Exception(trans('auth.email_does_not_exist'));
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function verifyCode(string $email, string $code)
    {
        try {
            $check = DB::table('password_reset_tokens')->where([
                ['email', $email], ['token', $code],
            ]);

            if ($check->exists()) {
                $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
                if ($difference > (int)Settings::group('otp')->get('otp_expire_time') * 60) {
                    throw new Exception(trans('auth.code_is_expired'));
                }
                $check->update(['isActive' => 0]);
                return trans('auth.you_can_reset_your_password');
            } else {
                throw new Exception(trans('auth.code_is_invalid'));
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = UserModel::where('email', $request->email)->first();
            if ($user) {
                $user->update(['password' => Hash::make($request->post('password'))]);
                $token  = $user->createToken($user->email)->plainTextToken;
                return [
                    'user'    => $user,
                    'token'   => $token,
                ];
            } else {
                throw new Exception(trans('common.something_went_wrong'));
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
