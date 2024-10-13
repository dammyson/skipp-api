<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Otp extends Model
{
    use HasFactory, UuidTrait;

    protected $guarded = ['id'];

    protected $fillable = [
        'email',
        'otp',
        'is_verified'
    ];
}
