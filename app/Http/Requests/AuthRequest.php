<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('auth.login')) {
            return [
                'email' => 'required|string|exists:users,email',
                'password' => 'required|string',
            ];
        } else if ($this->routeIs('auth.register')) {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:5|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
            ];
        } else if ($this->routeIs('auth.forgot')) {
            return [
                'email' => 'required|string|exists:users,email',
            ];
        } else if ($this->routeIs('auth.forgot.submit')) {
            return [
                'email' => 'required|string|exists:users,email',
                'password' => 'required|min:5|confirmed|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'password_confirmation' => 'required',
                'code' => 'required|string'
            ];
        } else if ($this->routeIs('auth.confirm')) {
            return [
                'email' => 'required|string|exists:users,email',
                'code' => 'required|numeric'
            ];
        }

        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator));
    }
}
