<?php

namespace App\Http\Requests;

use App\Rules\UniqueLikedProduct;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class LikedProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('liked-products.index')) {
            return [
                'page' => 'nullable|integer',
                'limit' => 'nullable|integer',
            ];
        } else if ($this->routeIs('liked-products.create')) {
            return [
                'product_variation_id' => [
                    'required',
                    'exists:product_variations,id',
                    new UniqueLikedProduct($this->user()->id)
                ],
            ];
        } else if ($this->routeIs('liked-products.delete')) {
            return [
                'product_variation_id' => [
                    'required',
                    'exists:product_variations,id'
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
