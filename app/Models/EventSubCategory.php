<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSubCategory extends Model
{
    //
    public function category() {
        return $this->belongsTo(EventCategory::class,'parent_id');
    }
}
