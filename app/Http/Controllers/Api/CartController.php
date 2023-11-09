<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Libraries\ResponseBase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\CartRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $products = Cart::with(['productVariation.product.productImages'])
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->where('order_id', null)
            ->get();

        return ResponseBase::success('Berhasil menerima data cart', $products);
    }

    public function create(CartRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        try {
            $isProductExist = Cart::where('user_id', $user->id)
                ->where('product_variation_id', $request->product_variation_id)
                ->first();

            if ($isProductExist) {
                $isProductExist->quantity += $request->quantity;
                $isProductExist->save();
                return ResponseBase::success('Berhasil menambahkan quantity product!', $isProductExist);
            } else {
                $cart = new Cart();
                $cart->user_id = $user->id;
                $cart->product_variation_id = $request->product_variation_id;
                $cart->quantity = $request->quantity;
                $cart->save();
            }

            return ResponseBase::success('Berhasil menambahkan data cart!', $cart);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan data cart -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal menambahkan data cart!', 409);
        }
    }

    public function update(CartRequest $request, Cart $cart)
    {
        try {
            $request->filled('quantity') ? $cart->quantity = $request->quantity : null;
            $request->filled('is_check') ? $cart->is_check = $request->is_check : null;
            $cart->save();

            return ResponseBase::success('Berhasil merubah data cart', $cart);
        } catch (\Exception $e) {
            Log::error('Gagal merubah data cart -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal merubah data cart', 409);
        }
    }

    public function delete(Cart $cart)
    {
        try {
            $cart->delete();
            return ResponseBase::success('Berhasil menghapus data cart');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus data cart -> ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal menghapus data cart', 409);
        }
    }
}
