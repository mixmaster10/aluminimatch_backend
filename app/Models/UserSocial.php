<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    protected $primaryKey = 'uid';

    protected $fillable = [
        'uid', 'facebook', 'twitter', 'linkedin', 'google', 'pinterest'
    ];

    protected $hidden = [
        'uid'
    ];

    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class, 'uid');
    }
}
