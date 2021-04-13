<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sid', 'rid', 'title', 'content', 'read'
    ];

    public function sender() {
        return $this->belongsTo(User::class, 'sid');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'rid');
    }

}
