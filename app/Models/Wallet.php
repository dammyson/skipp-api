<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Wallet extends Model
{

    use UuidTrait;
    protected $guarded = ['id'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'balance', 'ledger_balance', 'reference'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topUp(float $amount): bool
    {
        // Ensure the top-up amount is positive
        if ($amount <= 0) {
            return false; // Optionally, throw an exception or handle the error as needed
        }

        // Add the top-up amount to the current balance
        $this->balance += $amount;
        
        // Update the ledger balance, if necessary
        $this->ledger_balance += $amount;

        // Save the updated wallet
        return $this->save();
    }


    public function topDown(float $amount): bool
    {
        // Ensure the top-up amount is positive
        if ($amount <= 0) {
            return false; // Optionally, throw an exception or handle the error as needed
        }

        // Add the top-up amount to the current balance
        $this->balance -= $amount;
        
        // Update the ledger balance, if necessary
        $this->ledger_balance -= $amount;

        // Save the updated wallet
        return $this->save();
    }

}
