<?php

namespace App\Http\Controllers\Api;

use App\Libraries\ResponseBase;
use App\Models\ProductVariation;
use App\Http\Controllers\Controller;

class ProductVariationController extends Controller
{
    public function show($id)
    {
        $productVariation = ProductVariation::with('product')->findOrFail($id);
        return ResponseBase::success("Berhasil menerima data product category", $productVariation);
    }
}
