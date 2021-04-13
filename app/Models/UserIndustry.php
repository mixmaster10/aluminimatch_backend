<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserIndustry extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uid', 'industry'
    ];

    protected $hidden = [
        'uid'
    ];

    public function industry() {
        return $this->belongsTo(Industry::class, 'industry');
    }
}
