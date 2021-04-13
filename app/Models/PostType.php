<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostType extends Model
{   

    protected $fillable = ["name","shortDescription","icon"];

    protected $hidden = ["created_at","updated_at"];
}
