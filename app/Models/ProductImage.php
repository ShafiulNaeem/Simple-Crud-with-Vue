<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'file_path',
        'thumbnail',
        'created_at',
        'updated_at'
    ];
}
