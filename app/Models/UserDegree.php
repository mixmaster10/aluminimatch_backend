<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDegree extends Model
{
    protected $primaryKey = 'uid';

    protected $fillable = [
        'uid', 'type', 'degree', 'year', 'ibc'
    ];

    protected $hidden = [
        'uid'
    ];

    public $timestamps = false;

    public function degree() {
        return $this->belongsTo(Degree::class,'degree');
    }

    public function ibc() {
        return $this->belongsTo(Ibc::class,'ibc');
    }
}
