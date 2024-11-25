<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'seller') {
            return $this->is_seller;
        }

        if ($panel->getId() === 'owner') {
            return !$this->is_seller;
        }

        return false;
    }

    // public function canAccessTenant(Model $tenant): bool
    // {
    //     if ($this->is_Owner) {
    //         return $this->Owners->contains($tenant);
    //     } else {
    //         return $this->sellers->contains($tenant);
    //     }
    // }

    // public function getTenants(Panel $panel): array|Collection
    // {
    //     if ($this->is_Owner) {
    //         return $this->Owners;
    //     } else {
    //         return $this->sellers;
    //     }
    // }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->BelongsTo(Seller::class);
    }
}
