<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, UuidTrait;

    protected $guarded = ['id'];
    
    protected $fillable = ["name", "image_url"];

    public function products(): HasMany {
      return $this->hasMany(Product::class, 'category_id'); // Ensure the foreign key is correct
    }
  
    public function productCount(): int
    {
        return $this->products()->count();
    }
}
