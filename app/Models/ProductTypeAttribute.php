<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTypeAttribute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_translatable',
        'field',
        'required',
        'rank',
        'description',
        'unit',
        'is_variant_attribute',
        'validators',
        'product_type_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_translatable' => 'boolean',
        'required' => 'boolean',
        'is_variant_attribute' => 'boolean',
        'validators' => 'array',
        'product_type_id' => 'integer',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(ProductTypeAttributeOption::class);
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }
}
