<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = [
        'voucher_name',
        'voucher_desc',
        'code',
        'start_date',
        'end_date',
        'is_percent',
        'value',
        'is_active'
    ];

}
