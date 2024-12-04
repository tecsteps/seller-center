<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'seller_product_id',
        'seller_variant_id',
        'quantity',
        'price',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'order_id' => 'integer',
        'seller_product_id' => 'integer',
        'seller_variant_id' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function sellerProduct(): BelongsTo
    {
        return $this->belongsTo(SellerProduct::class);
    }

    public function sellerVariant(): BelongsTo
    {
        return $this->belongsTo(SellerVariant::class);
    }
}
