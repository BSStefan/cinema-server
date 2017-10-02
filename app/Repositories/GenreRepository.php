<?php

namespace App\Repositories;

use App\Models\Genre;
use App\Repositories\Eloquent\BasicRepository;

class GenreRepository extends BasicRepository
{
    protected $modelClass = Genre::class;
}