<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoldenProductVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'golden_product_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'golden_product_id' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(GoldenProductVariantLocalized::class);
    }

    public function goldenProduct(): BelongsTo
    {
        return $this->belongsTo(GoldenProduct::class);
    }
}
