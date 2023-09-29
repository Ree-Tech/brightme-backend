<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Libraries\ResponseBase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if($user->is_verif == 0)
                return ResponseBase::error('Akun anda belum di verifikasi, silahkan cek email!', 401);
        } catch (TokenExpiredException $e) {
            return ResponseBase::error('Token Expired', 401);
        } catch (TokenInvalidException $e) {
            return ResponseBase::error('Token Invalid', 401);
        } catch (\Exception $e) {
            return ResponseBase::error('Token Tidak Ditemukan', 401);
        }

        return $next($request);
    }
}
