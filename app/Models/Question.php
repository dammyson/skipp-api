<?php

namespace App\Models;


use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory, UuidTrait;

    protected $guarded = ['id'];

    protected $fillable = ['title', 'description', 'is_faq'];

    public function answers() {
        $this->hasMany(Answer::class);
    }
}
