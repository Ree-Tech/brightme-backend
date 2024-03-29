<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Survey;
use App\Models\GlowUpPlan;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function survey()
    {
        return $this->hasOne(Survey::class, 'user_id');
    }

    public function glowUpPlans()
    {
        return $this->hasMany(GlowUpPlan::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function likedProducts()
    {
        return $this->hasMany(LikedProduct::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }  
}
