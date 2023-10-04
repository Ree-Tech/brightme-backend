<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('cart.create')) {
            return [
                'product_variation_id' => 'required|exists:product_variations,id',
                'quantity' => 'required|numeric|integer|min:1',
            ];
        } else if ($this->routeIs('product.update')) {
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
