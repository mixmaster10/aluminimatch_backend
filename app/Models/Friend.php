<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uid', 'fid', 'shared'
    ];

    protected $hidden = [
        'uid', 'fid'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'uid');
    }

    public function friend() {
        return $this->belongsTo(User::class, 'fid');
    }
}
