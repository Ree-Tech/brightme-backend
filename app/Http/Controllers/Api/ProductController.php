<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Libraries\ResponseBase;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(ProductRequest $request)
    {
        $pageNumber = $request->input('page', 1);
        $dataAmount = $request->input('limit', 10);
        $search = $request->input('search', null);

        $products = Product::search($search)
            ->with(['productCategory', 'productVariations', 'productReviews', 'productImages'])
            ->paginate($dataAmount, ['*'], 'page', $pageNumber);

        return ResponseBase::success("Berhasil menerima data product", $products);
    }

    public function create(ProductRequest $request)
    {
        $slug = $this->slugGenerator($request->name);
        $images = $request->images;

        try {
            DB::beginTransaction();

            $product = new Product();
            $product->slug = $slug;
            $product->name = $request->name;
            $product->brand = $request->brand;
            $product->usage = $request->usage;
            $product->discount = $request->discount;
            $product->product_category_id = $request->product_category_id;
            $product->description = $request->description;
            $product->recommendations = $request->recommendations;
            $product->summary = $request->summary;
            $product->save();

            foreach ($images as $image) {
                $productImage = new ProductImage();
                $fileNameOriginal = $image->getClientOriginalName();
                $fileName = Str::slug(basename($fileNameOriginal, '.' . $image->getClientOriginalExtension()) . '-' . time());
                $fileNameExtension = $fileName  . '.' . $image->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('products', $image, $fileNameExtension);

                if (!$path)
                    return ResponseBase::error("Terjadi kesalahan upload gambar Product", 409);

                $productImage->product_id = $product->id;
                $productImage->img = $path;
                $productImage->save();
            }

            DB::commit();
            return ResponseBase::success("Berhasil menambahkan data product!", $product);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menambahkan data product: ' . $e->getMessage());
            return ResponseBase::error('Gagal menambahkan data product!', 409);
        }
    }

    public function show(Product $product)
    {
        return ResponseBase::success("Berhasil menerima data product", $product->load('productCategory', 'productVariations', 'productReviews', 'productImages'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            $request->filled('name') ? $product->name = $request->name : null;
            $request->filled('brand') ? $product->brand = $request->brand : null;
            $request->filled('usage') ? $product->usage = $request->usage : null;
            $request->filled('discount') ? $product->discount = $request->discount : null;
            $request->filled('product_category_id') ? $product->product_category_id = $request->product_category_id : null;
            $request->filled('description') ? $product->description = $request->description : null;
            $request->filled('recommendations') ? $product->recommendations = $request->recommendations : null;
            $request->filled('summary') ? $product->summary = $request->summary : null;

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
            DB::beginTransaction();
            $productImages = $product->productImages;
            foreach ($productImages as $productImage) {
                if (config('app.env') === 'local') {
                    $pathOld = str_replace(asset('storage/'), '', $productImage->img);
                } else {
                    $pathOld = str_replace(asset('public/storage/'), '', $productImage->img);
                }
                Storage::disk('public')->delete($pathOld);
                $productImage->delete();
            }

            $product->delete();

            DB::commit();
            return ResponseBase::success("Berhasil menghapus data product");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus data product: ' . $e->getMessage());
            return ResponseBase::error('Gagal menghapus data product', 409);
        }
    }

    public function slugGenerator($data)
    {
        $title = $data;
        $titleWords = explode(' ', $title);
        $titleWords = array_slice($titleWords, 0, 3);
        $slug = Str::slug(implode(' ', $titleWords));

        return $slug;
    }
}
