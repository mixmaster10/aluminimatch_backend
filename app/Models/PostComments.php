<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostComments extends Model
{
    protected $fillable = ["commentBy","postId","comment"];

    protected $hidden = ["updated_at"];

    public function commentUser() {
        return $this->belongsTo(User::class, 'commentBy')->select(["id","first_name","last_name","avatar","online"]);
    }

}
