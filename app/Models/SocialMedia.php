<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMedia extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'social_media',
        'url',
        'is_active',
        'is_thumbnail',
        'icon',
    ];

    protected $dates = ['deleted_at'];
}
