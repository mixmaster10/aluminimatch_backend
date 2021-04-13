<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHobby extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uid', 'hobby', 'skill_scale', 'match_scale', 'teach_scale'
    ];

    protected $hidden = [
        'uid'
    ];

    public function hobby() {
        return $this->belongsTo(Hobby::class, 'hobby');
    }
}
