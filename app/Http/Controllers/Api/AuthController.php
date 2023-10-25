<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Carbon\Carbon;
use App\Models\User;
use Jenssegers\Agent\Agent;
use App\Libraries\ResponseBase;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials))
            return ResponseBase::error("Password salah", 403);

        return ResponseBase::success('Login berhasil', ['token' => $token, 'type' => 'bearer']);
    }

    public function register(AuthRequest $request)
    {
        $data = [];
        $data['email'] = $request->email;
        $data['subject'] = 'OTP Verification';
        $data['message'] = 'register';

        try {
            DB::beginTransaction();

            $user = new User();
            $user->role_id = 1;
            $user->name = $request->name;
            $user->email = strtolower($request->email);
            $user->password = Hash::make($request->password);
            $user->save();

            $this->sendEmail($data);

            DB::commit();
            return ResponseBase::success('Berhasil register!', $user);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal register -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal register!', 409);
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            $authUser = $this->findOrCreateUser($user);
            $token = JWTAuth::fromUser($authUser);

            return ResponseBase::success('Login berhasil', ['token' => $token, 'type' => 'bearer']);
        } catch (\Exception $e) {
            Log::error('Gagal autentikasi google -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error("Gagal autentikasi google : " . $e->getMessage(), 403);
        }
    }

    private function findOrCreateUser($googleUser)
    {
        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            return $user;
        } else {
            try {
                $user = new User();
                $user->role_id = 1;
                $user->name = $googleUser->name;
                $user->email = $googleUser->email;
                $user->is_verif = 1;
                $user->save();

                return $user;
            } catch (\Exception $e) {
                Log::error('Gagal register -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
                return ResponseBase::error('Gagal register!', 409);
            }
        }
    }

    public function sendEmailForgot(AuthRequest $request){
        $data = [];
        $data['email'] = $request->email;
        $data['subject'] = 'Forgot Password Code';
        $data['message'] = 'Forgot Password';

        try {
            $this->sendEmail($data);
            return ResponseBase::success('Berhasil kirim email forgot password');
        } catch (\Exception $e) {
            Log::error('Gagal kirim email forgot password -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal kirim email forgot password!', 409);
        }

        return $this->sendEmail($data);
    }

    public function sendEmail($data = [])
    {
        $user = User::where('email', $data['email'])->firstOrFail();
        $code = rand(1000, 9999);
        $agent = new Agent();
        $os = $agent->platform();
        $browserName = $agent->browser();
        
        try {
            DB::beginTransaction();
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $code, 'created_at' => Carbon::now()]
            );

            Mail::send('email.otp', [
                'code' => $code,
                'user' => $user,
                'os' => $os,
                'browserName' => $browserName
            ], function ($message) use ($data) {
                $message->to($data['email']);
                $message->subject($data['subject']);
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal kirim email ' . $data['message'] . ' -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
        }
    }

    public function confirmCode(AuthRequest $request)
    {
        $resetData = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->code
            ])
            ->first();

        if (!$resetData) {
            return ResponseBase::error('Token tidak valid!');
        }

        $tokenCreatedAt = Carbon::parse($resetData->created_at);
        $tokenExpiryTime = $tokenCreatedAt->addHours(24);
        $currentRouteName = Route::currentRouteName();

        try {
            DB::beginTransaction();
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            if (Carbon::now()->greaterThan($tokenExpiryTime)) {
                DB::commit();
                return ResponseBase::error('Token reset password sudah kedaluwarsa, harap meminta token baru!');
            }

            if ($currentRouteName == 'auth.confirm') {
                User::where('email', $request->email)
                ->update(['is_verif' => 1]);
                $message = 'konfirmasi email!';
            }

            if ($currentRouteName == 'auth.forgot.submit') {
                User::where('email', $request->email)
                ->update(['password' => Hash::make($request->password)]);
                $message = 'merubah password!';
            }

            DB::commit();
            return ResponseBase::success('Berhasil ' . $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal ' . $message . ' -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal ' . $message, 409);
        }
    }

    public function logout()
    {
        JWTAuth::invalidate();

        return ResponseBase::success('Logout berhasil');
    }
}
