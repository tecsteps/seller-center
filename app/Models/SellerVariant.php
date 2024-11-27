<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'attributes',
        'seller_product_id',
        'seller_id',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'attributes' => 'array',
        'seller_product_id' => 'integer',
        'seller_id' => 'integer',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(SellerProductImage::class);
    }

    public function sellerProduct(): BelongsTo
    {
        return $this->belongsTo(SellerProduct::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
