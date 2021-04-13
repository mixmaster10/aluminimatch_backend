<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ["title","description","photoUrl","postTypeId", "postCategoryId", "userId", "college", "embed"];

    protected $hidden = ["postTypeId","postCategoryId","userId","updated_at"];

    public function user() {
        return $this->belongsTo(User::class, 'userId')->select(["id","first_name","last_name","avatar","online","college"]);
    }

    public function type() {
        return $this->belongsTo(PostType::class, 'postTypeId');
    }

    public function category() {
        return $this->belongsTo(PostCategory::class, 'postCategoryId');
    }

    public function likes()
    {
        return $this->hasMany(PostLikes::class,"postId");
    }

    public function comments()
    {
        return $this->hasMany(PostComments::class,"postId")->with("commentUser");
    }

}
