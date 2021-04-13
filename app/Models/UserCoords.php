<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoords extends Model
{
    protected $primaryKey = 'uid';

    public $timestamps = false;

    protected $fillable = [
        'uid', 'lat', 'lng', 'show'
    ];

    protected $hidden = [
        'uid'
    ];

    protected $casts = [
        'lat' => 'double',
        'lng' => 'double'
    ];
}
