<?php

namespace App\Http\Controllers\Api;

use App\Libraries\ResponseBase;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $products = ProductCategory::with('products.productVariations')->get();

        return ResponseBase::success("Berhasil menerima data product category", $products);
    }

    public function show(ProductCategory $productCategory)
    {
        return ResponseBase::success("Berhasil menerima data product category", $productCategory->load('products.productVariations'));
    }
}
