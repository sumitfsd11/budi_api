<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $fillable = [
        'headline',
        'description',
        'image',
    ];

    public function getImageAttribute($value)
    {
        return $value ? asset('storage/'.$value) : null;
    }

  

    
}
