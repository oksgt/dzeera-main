<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'trans_number',
        'user_id',
        'product_id',
        'color_opt_id',
        'size_opt_id',
        'qty',
        'price',
        'is_gift',
    ];
}
