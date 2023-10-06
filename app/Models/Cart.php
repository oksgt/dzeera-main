<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'email',
        'data',
        'product_id',
        'color_opt_id',
        'size_opt_id',
        'qty',
        'price',
    ];
}
