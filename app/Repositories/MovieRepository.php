<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Repositories\Eloquent\BasicRepository;

class MovieRepository extends BasicRepository
{
    protected $modelClass = Movie::class;
}