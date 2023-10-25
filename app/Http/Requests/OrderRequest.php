<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('order.create')) {
            return [
                'carts_id' => 'required|array',
                'payment_type' => 'required',
            ];
        } else if ($this->routeIs('order.update')) {
            return [
                'quantity' => 'required|numeric|integer|min:1',
            ];
        }

        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator));
    }
}
