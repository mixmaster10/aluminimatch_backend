<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMatchWeight extends Model
{
    protected $primaryKey = 'uid';

    protected $fillable = [
        'uid', 'ps', 'cl'
    ];

    protected $hidden = [
        'uid'
    ];

    public $timestamps = false;
}
