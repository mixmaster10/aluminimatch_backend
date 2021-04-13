<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Millitary extends Model
{
    protected $fillable = ['military_branch','code','description','rank','section','similar_codes'];
}
