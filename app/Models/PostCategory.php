<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    protected $fillable = ["name","shortDescription","icon","postTypeId"];
    
    protected $hidden = ["postTypeId","created_at","updated_at"];
}
