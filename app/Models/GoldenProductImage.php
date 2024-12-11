<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoldenProductImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'golden_product_id',
        'image',
        'number',
        'seller_product_image_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'golden_product_id' => 'integer',
        'seller_product_image_id' => 'integer',
    ];

    public function goldenProduct(): BelongsTo
    {
        return $this->belongsTo(GoldenProduct::class);
    }

    public function sellerProductImage(): BelongsTo
    {
        return $this->belongsTo(SellerProductImage::class);
    }
}
