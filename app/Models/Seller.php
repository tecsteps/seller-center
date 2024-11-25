<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'status',
        'user_id',
        'email',
        'account_holder_name',
        'description',
        'company_name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country_code',
        'phone',
        'vat',
        'tin',
        'eori',
        'iban',
        'swift_bic',
        'bank_name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    public function sellerData(): HasOne
    {
        return $this->hasOne(SellerData::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
