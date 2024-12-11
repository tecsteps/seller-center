<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoldenProductAttribute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_type_attribute_id',
        'golden_product_id',
        'is_option',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'product_type_attribute_id' => 'integer',
        'golden_product_id' => 'integer',
        'is_option' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(GoldenProductAttributeValue::class);
    }

    public function productTypeAttributeOptionValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductTypeAttributeOptionValue::class);
    }

    public function productTypeAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductTypeAttribute::class);
    }

    public function goldenProduct(): BelongsTo
    {
        return $this->belongsTo(GoldenProduct::class);
    }
}
