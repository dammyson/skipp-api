<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;



class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'store_id'];

    public static function boot()
    {
        parent::boot();

        // Automatically generate a UUID for the id field if it's not set
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

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
