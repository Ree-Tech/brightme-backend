<?php

namespace App\Http\Controllers\Api;

use App\Libraries\ResponseBase;
use App\Models\ProductVariation;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariationRequest;
use Illuminate\Support\Facades\Log;

class ProductVariationController extends Controller
{
    public function store(ProductVariationRequest $request)
    {
        try {
            $productVariation = new ProductVariation();
            $productVariation->product_id = $request->product_id;
            $productVariation->name = $request->name;
            $productVariation->price = $request->price;
            $productVariation->save();

            return ResponseBase::success("Berhasil menambahkan data product variation!", $productVariation->load('product'));
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan data product variation: ' . $e->getMessage());
            return ResponseBase::error('Gagal menambahkan data product variation!', 409);
        }
    }

    public function show($id)
    {
        $productVariation = ProductVariation::with('product')->findOrFail($id);
        return ResponseBase::success("Berhasil menerima data product category", $productVariation);
    }
}
