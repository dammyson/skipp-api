<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;

class User  extends Authenticatable implements HasName
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 
        'last_name',
        'email',
        'phone_number',
        'password',
        'name',
        'pin_number',
        'business_name',
        'store_type',
        'business_address',
        'user_type',
        'image_url',
        'store_policy',
        'store_address'
        
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

        public function getFilamentName(): string
        {
            return "{$this->first_name} {$this->last_name}";
        }

    public function fulfilmentMethods() {
        return $this->belongsToMany(FulfilmentMethod::class);
    }
    
    /**
     * Define a one-to-one relationship with Wallet.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->wallet()->create([
               'balance' => 0,
               'ledger_balance' => 0 
            ]);
        });
    }
}
