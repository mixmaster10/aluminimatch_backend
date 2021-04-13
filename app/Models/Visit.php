<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $primaryKey = 'uid';

    protected $hidden = [
        'uid', 'vid'
    ];

    protected $fillable = [
        'uid', 'vid', 'count'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'uid');
    }
}
