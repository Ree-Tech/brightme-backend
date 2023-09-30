<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Libraries\ResponseBase;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    public function index(ProductRequest $request)
    {
        $pageNumber = $request->input('page', 1);
        $dataAmount = $request->input('limit', 10);
        $search = $request->input('search', null);

        $products = Product::search($search)
        ->with(['productCategory', 'productVariations', 'productReviews'])
            ->paginate($dataAmount, ['*'], 'page', $pageNumber);

        return ResponseBase::success("Berhasil menerima data product", $products);
    }

    public function create(ProductRequest $request)
    {
        $slug = $this->slugGenerator($request->name);

        try {
            $product = new Product();
            $product->slug = $slug;
            $product->name = $request->name;
            $product->usage = $request->usage;
            $product->discount = $request->discount;
            $product->product_category_id = $request->product_category_id;
            $product->description = $request->description;
            $product->save();

            return ResponseBase::success("Berhasil menambahkan data product!", $product);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan data product: ' . $e->getMessage());
            return ResponseBase::error('Gagal menambahkan data product!', 409);
        }
    }

    public function show(Product $product)
    {
        return ResponseBase::success("Berhasil menerima data product", $product->load('productCategory', 'productVariations', 'productReviews'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            $request->filled('name') ? $product->name = $request->name : null;
            $request->filled('usage') ? $product->usage = $request->usage : null;
            $request->filled('discount') ? $product->discount = $request->discount : null;
            $request->filled('product_category_id') ? $product->product_category_id = $request->product_category_id : null;
            $request->filled('description') ? $product->description = $request->description : null;

            $slug = $this->slugGenerator($product->name);

            $product->slug = $slug;
            $product->save();

            return ResponseBase::success("Berhasil merubah data product", $product);
        } catch (\Exception $e) {
            Log::error('Gagal merubah data product: ' . $e->getMessage());
            return ResponseBase::error('Gagal merubah data product', 409);
        }
    }

    public function delete(Product $product)
    {
        try {
            $product->delete();
            return ResponseBase::success("Berhasil menghapus data product");
        } catch (\Exception $e) {
            Log::error('Gagal menghapus data product: ' . $e->getMessage());
            return ResponseBase::error('Gagal menghapus data product', 409);
        }
    }

    public function slugGenerator($data){
        $title = $data;
        $titleWords = explode(' ', $title);
        $titleWords = array_slice($titleWords, 0, 3);
        $slug = Str::slug(implode(' ', $titleWords));

        return $slug;
    }
}
