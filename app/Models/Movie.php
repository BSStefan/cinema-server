<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    public function genres() : BelongsToMany
    {
        return $this->belongsToMany('App\Models\Genre');
    }

    public function directors() : BelongsToMany
    {
        return $this->belongsToMany('App\Models\Director');
    }

    public function actors() : BelongsToMany
    {
        return $this->belongsToMany('App\Models\Actor');
    }
}
