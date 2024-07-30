<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

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
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'barcode_formats' => 'array',
        'ingredients' => 'array',
        'nutrition_facts' => 'array',
    ];

    /**
     * Get the store that owns the product.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
