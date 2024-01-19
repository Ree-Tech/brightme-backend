<?php

namespace App\Http\Controllers\Api;

use App\Models\LikedProduct;
use App\Libraries\ResponseBase;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\LikedProductRequest;
use App\Http\Resources\LikedProductResource;

class LikedProductController extends Controller
{
    public function index(LikedProductRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $pageNumber = $request->input('page', 1);
        $dataAmount = $request->input('limit', 10);

        $likedProducts = $user->likedProducts()->with('productVariation.product')
            ->paginate($dataAmount, ['*'], 'page', $pageNumber);

        return ResponseBase::success('Berhasil menerima data liked product', new LikedProductResource($likedProducts));
    }

    public function create(LikedProductRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        try {
            $likedProduct = new LikedProduct();
            $likedProduct->user_id = $user->id;
            $likedProduct->product_variation_id = $request->product_variation_id;

            $likedProduct->save();

            return ResponseBase::success('Berhasil menambahkan data liked product!', $likedProduct);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan data liked product -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal menambahkan data likedProduct!', 409);
        }
    }

    public function delete(LikedProduct $likedProduct)
    {
        try {
            $likedProduct->delete();

            return ResponseBase::success('Berhasil menghapus data liked product');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus data liked product -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal menghapus data liked product!', 409);
        }
    }
}
