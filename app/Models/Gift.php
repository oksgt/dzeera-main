<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gift extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'gift_name',
        'gift_description',
        'is_for_first_purchase',
        'min_purchase_value',
        'product_opt_id',
        'is_active',
    ];

    public function getGiftNameAttribute()
    {
        return $this->attributes['gift_name'];
    }
}
