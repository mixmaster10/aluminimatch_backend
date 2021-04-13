<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'companyName', 'creatorTitle', 'creator_id', 'companyStartedOn', 'description', 'leadsBalance', 'paid', 'websiteLink', 'videoLink', 'photoUrl'
    ];

    // protected $with = ['ads'];

    public function users() {
        return $this->belongsToMany('App\Models\User','user_company');
    }

    public function creator() {
        return $this->belongsTo('App\Models\User','creator_id');
    }

    public function ads() {
        return $this->hasMany('App\Models\Ad');
    }
}
