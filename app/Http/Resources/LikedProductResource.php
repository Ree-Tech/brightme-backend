<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LikedProductResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'product_variation_id' => $item->productVariation->id,
                'name' => $item->productVariation->product->name,
                'brand' => $item->productVariation->product->brand,
                'discount' => $item->productVariation->product->discount,
                'img' => $item->productVariation->product->productImages,
                'price' => $item->productVariation->price,
            ];
        });
    }
}
