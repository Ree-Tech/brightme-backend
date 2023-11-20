<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DetectionRequest;
use App\Libraries\ResponseBase;
use App\Models\Product;
use GuzzleHttp\Client;

class DetectionController extends Controller
{
    public function detection(DetectionRequest $request)
    {
        $client = new Client();
        $response = $client->get('http://brightme.reetech.id/recomendation');

        if ($response->getStatusCode() == 200) {
            $responseData = json_decode($response->getBody()->getContents(), true);

            $input1 = $responseData['data']['input1'];
            $input2 = $responseData['data']['input2'];

            $products = Product::with('productVariations')->where(function ($query) use ($input1, $input2) {
                $query->where('recommendations', 'LIKE', '%' . $input1 . '%')
                    ->orWhere('recommendations', 'LIKE', '%' . $input2 . '%');
            })->get();

            $data = [
                'face_conditions' => $input1 . ' and ' . $input2,
                'product_recommendations' => $products
            ];
            return ResponseBase::success('Berhasil kirim email forgot password', $data);
        } else {
            return ResponseBase::error('Kesalahan Server Machine Learning');
        }
    }
}
