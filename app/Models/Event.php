<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'creator_id', 'title', 'description', 'minimum_required', 'max_needed', 'active', 'start_at', 'end_at', 'address', 'city', 'state', 'country', 'zip_code', 'meeting_type', 'meeting_link', 'meeting_id', 'meeting_passcode', 'number_of_participants', 'rsvp_yes', 'rsvp_interested', 'comment_count', 'comment_table_id_link'
    ];

    public function category() {
        return $this->belongsTo(EventSubCategory::class);
    }

    public function creator() {
        return $this->belongsTo(User::class,'creator_id');
    }
}
