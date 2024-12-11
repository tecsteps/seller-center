<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTypeAttributeOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_type_attribute_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'product_type_attribute_id' => 'integer',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(ProductTypeAttributeOptionValue::class);
    }

    public function productTypeAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductTypeAttribute::class);
    }
}
