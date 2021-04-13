<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description','company_id','active','leadsRemaining','totalLeads','comment_count','isLiked','likes_count','websiteLink','audience','photoUrl','leadsUsed'
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

    public function user() {
        return $this->hasOneThrough('App\Models\User','App\Models\Company');
    }
}
