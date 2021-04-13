<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'title'
    ];
    
    public function events() {
        return $this->hasMany(Event::class);
    }

    public function subCategories() {
        return $this->hasMany(EventCategory::class,'parent_id');
    }
}
