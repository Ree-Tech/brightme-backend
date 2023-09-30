<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class GlowUpPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('glow-up.index')) {
            return [
                'page' => 'nullable|integer',
                'limit' => 'nullable|integer',
            ];
        } else if ($this->routeIs('glow-up.create')) {
            return [
                'img' => 'required|image|mimes:jpeg,png,jpg,gif',
            ];
        }

        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator));
    }
}
