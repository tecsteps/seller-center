<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seller extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'hideProducts',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'hideProducts' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function sellerData(): HasOne
    {
        return $this->hasOne(SellerData::class);
    }

    public function partnership(): HasOne
    {
        return $this->hasOne(Partnership::class);
    }

    public function sellerProducts(): HasMany
    {
        return $this->hasMany(SellerProduct::class);
    }

    public function sellerVariants(): HasMany
    {
        return $this->hasMany(SellerVariant::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
