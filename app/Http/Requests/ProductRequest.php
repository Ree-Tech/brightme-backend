<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('product.index')) {
            return [
                'page' => 'nullable|integer',
                'limit' => 'nullable|integer',
                'search' => 'nullable|string',
            ];
        } else if ($this->routeIs('product.create')) {
            return [
                'name' => 'required|string|unique:products|max:255',
                'brand' => 'required|string',
                'usage' => 'required|string',
                'discount' => 'required|numeric',
                'product_category_id' => 'required|exists:product_categories,id',
                'description' => 'required|string',
                'recommendations' => 'required|string',
                'summary' => 'required|string',
                'images' => 'required|array',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif'
            ];
        } else if ($this->routeIs('product.update')) {
            return [
                'name' => 'nullable|string|max:255|unique:products,name,' . $this->route('product')->id,
                'brand' => 'nullable|string',
                'usage' => 'nullable|string',
                'discount' => 'nullable|numeric',
                'product_category_id' => 'nullable|exists:product_categories,id',
                'description' => 'nullable|string',
                'recommendations' => 'nullable|string',
                'summary' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif'
            ];
        }

        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator));
    }
}
