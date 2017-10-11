<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cinema extends Model
{
    public function projections() : HasMany
    {
        return $this->hasMany('App\Models\Projection');
    }

    public function soon_movie() : HasMany
    {
        return $this->hasMany('App\Models\SoonMovie');
    }
}
