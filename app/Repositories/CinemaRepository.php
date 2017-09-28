<?php

namespace App\Repositories;

use App\Models\Cinema;
use App\Repositories\Eloquent\BasicRepository;

class CinemaRepository extends BasicRepository
{
    protected $modelClass = Cinema::class;
}