<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = ['uid','parents_from_alma','siblings_from_alma','play_video_games','military_rank','military_code','video_games_frequency','video_games_categories','video_games_fav_title','athletic_stuff_you_play','have_exotic_pet','havePet','pet','fan_of_alma_football','fan_of_alma_basketball','in_us_military','military_type','dependent_us_military_person','instrument','long_have_lived_here','country_to_travel','state_to_travel','city_to_travel'];
    // protected $casts = [
    //     'video_games_fav_title' => 'array',
    //     'athletic_stuff_you_play' => 'array'

    // ];
}
