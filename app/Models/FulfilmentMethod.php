<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FulfilmentMethod extends Model
{
     
    protected $fillable = ['method_name'];
    
     public function users() {
        return $this->BelongsToMany(User::class);
    }

}
