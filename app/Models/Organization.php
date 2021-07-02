<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'website', 'picture_id'
    ];

    public function picture()
    {
        return $this->belongsTo(Picture::class, 'picture_id', 'id');
    }
}
