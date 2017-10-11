<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    public function genres() : BelongsToMany
    {
        return $this->belongsToMany('App\Models\Genre')->withTimestamps();
    }

    public function directors() : BelongsToMany
    {
        return $this->belongsToMany('App\Models\Director')->withTimestamps();
    }

    public function actors() : BelongsToMany
    {
        return $this->belongsToMany('App\Models\Actor')->withTimestamps();
    }

    public function projections() : HasMany
    {
        return $this->hasMany('App\Models\Projections');
    }

    public function soon_movie() : HasMany
    {
        return $this->hasMany('App\Models\SoonMovie');
    }
}
