<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, UuidTrait;

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'code',
        'barcode_number',
        'barcode_formats',
        'mpn',
        'model',
        'asin',
        'title',
        'category',
        'manufacturer',
        'serial_number',
        'weight',
        'dimension',
        'warranty_length',
        'brand',
        'ingredients',
        'nutrition_facts',
        'size',
        'description',
        'quantity',
        'price'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }


    public function incrementQuantity(int $amount): void
    {
        $this->increment('quantity', $amount);
    }

    public function decrementQuantity(int $amount): bool
    {
        if ($this->quantity >= $amount) {
            $this->decrement('quantity', $amount);
            return true;
        }

        return false;
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
