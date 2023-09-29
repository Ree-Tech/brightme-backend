<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class SurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $skinTypes = ['Normal Skin', 'Oily Skin', 'Dry Skin', 'Sensitive Skin', 'Combination Skin'];
        $skinProblems = ['Acne', 'Wrinkles', 'Black spots', 'Puff eyes'];

        if ($this->routeIs('survey.create')) {
            return [
                'age' => 'required|numeric|min:1',
                'skin_type' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::in($skinTypes), // Menentukan bahwa hanya nilai dalam $skinTypes yang diperbolehkan
                ],
                'skin_problem' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::in($skinProblems), // Menentukan bahwa hanya nilai dalam $skinProblems yang diperbolehkan
                ],
            ];
        }

        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator));
    }
}
