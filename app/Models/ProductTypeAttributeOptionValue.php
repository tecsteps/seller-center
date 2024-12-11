<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductTypeAttributeOptionValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
        'locale_id',
        'product_type_attribute_option_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'locale_id' => 'integer',
        'product_type_attribute_option_id' => 'integer',
    ];

    public function goldenProductAttributes(): BelongsToMany
    {
        return $this->belongsToMany(GoldenProductAttribute::class);
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    public function productTypeAttributeOption(): BelongsTo
    {
        return $this->belongsTo(ProductTypeAttributeOption::class);
    }
}
