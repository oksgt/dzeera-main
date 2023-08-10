<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

    public $fillable = ['brand_name'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source'    => 'brand_name'
            ]
        ];
    }
}
