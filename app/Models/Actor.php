<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Actor extends Model
{
    public function movies() : BelongsToMany
    {
        return $this->belongsToMany('App\Models\Movie');
    }
}