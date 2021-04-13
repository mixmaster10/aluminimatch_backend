<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReports extends Model
{
    //
    protected $fillable = ["reportedBy","postId","reason","created_at"];

    protected $hidden = ["updated_at"];

    public function reportedBy() {
        return $this->belongsTo(User::class, 'reportedBy')->select(["id","first_name","last_name","avatar","online"]);
    }

    public function reportedPost() {
        return $this->belongsTo(Post::class, 'postId');
    }
}
