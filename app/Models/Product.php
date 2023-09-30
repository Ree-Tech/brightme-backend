<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\ProductReview;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
            ->orWhere('slug', 'like', "%$search%");
        });
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
    }
}
