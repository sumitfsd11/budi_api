<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable=[
        'noticeimage',
        'title',
        'description'
    ];

    public function getNoticeimageAttribute($value)
    {
        return $value ? asset('storage/'.$value) : null;
    }
}
