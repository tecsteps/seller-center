<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoldenProductVariantLocalized extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'attributes',
        'golden_product_variant_id',
        'locale_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'attributes' => 'array',
        'golden_product_variant_id' => 'integer',
        'locale_id' => 'integer',
    ];

    public function goldenProductVariant(): BelongsTo
    {
        return $this->belongsTo(GoldenProductVariant::class);
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
