<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use App\Models\User;
use App\Libraries\ResponseBase;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUserProfile()
    {
        $user = JWTAuth::parseToken()->authenticate();

        return ResponseBase::success('Berhasil mendapatkan data profile', ['user' => $user]);
    }

    public function update(UserRequest $request)
    {
        try {
            $id = JWTAuth::parseToken()->authenticate()->id;
            $user = User::findOrFail($id);

            $user->name = $request->filled('name') ? $request->name : $user->name;
            $user->email = $request->filled('email') ? $request->email : $user->email;
            $user->password = $request->filled('password') ? Hash::make($request->password) : $user->password;
            $user->phone = $request->filled('phone') ? $request->phone : $user->phone;
            $user->address = $request->filled('address') ? $request->address : $user->address;
            $user->birthdate = $request->filled('birthdate') ? $request->birthdate : $user->birthdate;
            $user->role_id = $request->filled('role_id') ? $request->role_id : $user->role_id;
            $user->save();

            return ResponseBase::success("Berhasil update data user", $user);
        } catch (\Exception $e) {
            return ResponseBase::error($e->getMessage(), 409);
        }
    }
}
