<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlowUpPlan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getImgAttribute()
    {
        if ($this->attributes['img'] == null)
            return null;
            
        if (config('app.env') === 'local') {
            // Jika aplikasi berjalan di localhost (mode pengembangan)
            return asset('storage/' . $this->attributes['img']);
        } else {
            // Jika aplikasi berjalan di lingkungan selain localhost (mode produksi)
            return asset('public/storage/' . $this->attributes['img']);
        }
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
