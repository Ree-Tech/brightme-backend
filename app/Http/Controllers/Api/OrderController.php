<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\MidtransHistory;
use Illuminate\Support\Str;
use App\Libraries\ResponseBase;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(OrderRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $status = $request->input('status', null);

        $orders = Order::with(['user', 'carts.productVariation.product'])
            ->status($status)
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return ResponseBase::success('Berhasil menerima data order', $orders);
    }

    public function create(OrderRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        try {
            DB::beginTransaction();

            $orderId = Str::uuid()->toString();

            $order = new Order();
            $order->user_id = $user->id;
            $order->price = 0;
            $order->additional_cost = 0;
            $order->payment_type = $request->payment_type;
            $order->status = 'Pending';
            $order->tracking_number = null;
            $order->invoice_id = $orderId;

            $cartsId = $request->carts_id;

            foreach ($cartsId as $cartId) {
                $cart = Cart::findOrFail($cartId);

                $productVariation = $cart->productVariation;

                $itemTotalPrice = $productVariation->price * $cart->quantity;

                $order->price += $itemTotalPrice;

                $cart->order_id = $order->id;
                $cart->save();
            }

            $order->save();

            $midtransEnv = config('midtrans.midtrans_env');
            $serverKey = config('midtrans.' . $midtransEnv . '_server_key');

            $response = Http::withBasicAuth($serverKey, '')
            ->post(config('midtrans.' . $midtransEnv . '_url'), [
                'payment_type' => 'bank_transfer',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $order->price,
                ],
                'bank_transfer' => [
                    'bank' => $request->bank,
                ],
                'customer_details' => [
                    'email' => $user->email,
                    'first_name' => 'CUSTOMER ',
                    'last_name' => $user->name,
                    'phone' => $user->phone,
                ]
            ]);

            if($response->failed()) {
                return response()->json([
                    'message' => 'failed charge'
                ], 500);
            }

            $result = $response->json();
            if($result['status_code'] != '201') {
                return response()->json([
                    'message' => $result['status_message']
                ], 500);
            }

            DB::commit();
            return ResponseBase::success('Berhasil menambahkan data order!', $result);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menambahkan data order => ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal menambahkan data order!', 409);
        }
    }

    public function update(OrderRequest $request, Order $order)
    {
        try {
            $request->filled('quantity') ? $order->quantity = $request->quantity : null;
            $order->save();

            return ResponseBase::success('Berhasil merubah data order', $order);
        } catch (\Exception $e) {
            Log::error('Gagal merubah data order => ' . $e->getFile() . ':' . $e->getLine() . ' => ' . $e->getMessage());
            return ResponseBase::error('Gagal merubah data order', 409);
        }
    }


    public function callback(Request $request) {
        $payload = $request->all();

        Log::info('incoming-midtrans', [
            'payload' => $payload
        ]);

        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];

        $reqSignature = $payload['signature_key'];

        $midtransEnv = config('midtrans.midtrans_env');
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.' . $midtransEnv . '_server_key'));

        if($signature != $reqSignature) {
            return response()->json([
                'message' => 'invalid signature'
            ], 401);
        }

        $transactionStatus = $payload['transaction_status'];

        $midtransHistory = new MidtransHistory;
        $midtransHistory->order_id = $orderId;
        $midtransHistory->status = $transactionStatus;
        $midtransHistory->payload = json_encode($payload);
        $midtransHistory->save();

        $order = Order::where('invoice_id', $orderId)->firstOrFail();
        if(!$order) {
            return response()->json([
                'message' => 'invalid order id'
            ], 400);
        }

        if($transactionStatus == 'settlement') {
            $order->status = 'PAID';
            $order->save();
        } else if($transactionStatus == 'expire') {
            $order->status = 'EXPIRED';
            $order->save();
        }

        return response()->json([
            'message' => 'success',
        ]);
    }
}
