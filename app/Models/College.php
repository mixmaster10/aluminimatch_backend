<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    public $timestamps = false;

    protected $hidden = [
        'country_id', 'state_id'
    ];

    protected $fillable = [
        'name', 'color1', 'color2', 'logo1', 'logo2', 'slogan', 'acronym', 'banner', 'country_id', 'state_id'
    ];

    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state() {
        return $this->belongsTo(State::class, 'state_id');
    }
}
