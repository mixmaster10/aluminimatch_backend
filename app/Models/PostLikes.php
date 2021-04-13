<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostLikes extends Model
{
    protected $fillable = ["likedBy","postId","likedBy"];

    protected $hidden = ["created_at","updated_at"];

    public function likeBy() {
        return $this->belongsTo(User::class, 'likedBy')->select(["id","first_name","last_name","avatar","online"]);
    }
}
