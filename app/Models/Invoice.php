<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, UuidTrait;

    protected $guarded = ['id'];

    protected $fillable = ['user_id', 'store_id', 'total_amount', 'fulfilment_method'];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
