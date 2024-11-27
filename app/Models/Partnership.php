<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partnership extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'rejection_reason',
        'take_all_products',
        'seller_id',
        'notes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'take_all_products' => 'boolean',
        'seller_id' => 'integer',
    ];

    /**
     * Eager load relationships by default
     */
    protected $with = ['seller', 'seller.sellerData'];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
