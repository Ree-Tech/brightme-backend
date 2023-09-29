<?php

namespace App\Http\Requests;

use JWTAuth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = JWTAuth::parseToken()->authenticate()->id;

        if ($this->routeIs('user.update')) {
            return [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $id,
                'password' => 'nullable|min:5|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'phone' => 'nullable|numeric|starts_with:62|min:5',
                'address' => 'nullable|string|max:255',
                'birthdate' => 'nullable|date',
                'role_id' => 'nullable|numeric|exists:roles,id',
            ];
        }

        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator));
    }
}
