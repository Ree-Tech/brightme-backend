<?php

namespace App\Rules;

use App\Models\LikedProduct;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueLikedProduct implements ValidationRule
{
    protected $userId;


    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isExists = LikedProduct::where('user_id', $this->userId)
            ->where('product_variation_id', $value)
            ->exists();

        if ($isExists) {
            $fail('Produk varian yang dipilih sudah disukai oleh pengguna.');
        }
    }
}
