<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_one',
        'product_variant_two',
        'product_variant_three',
        'price',
        'stock',
        'created_at',
        'updated_at'
    ];

    public function variantOne()
    {
        return $this->belongsTo('App\Models\ProductVariant','product_variant_one');
    }
    public function variantTwo()
    {
        return $this->belongsTo('App\Models\ProductVariant','product_variant_two');
    }
    public function variantThree()
    {
        return $this->belongsTo('App\Models\ProductVariant','product_variant_three');
    }


}
