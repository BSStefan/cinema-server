<?php

namespace App\Repositories;

use App\Models\Director;
use App\Repositories\Eloquent\BasicRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DirectorRepository extends BasicRepository
{
    protected $modelClass = Director::class;

    public function saveMovieDirectors(array $directors) : array
    {
        $directorsId = [];
        foreach($directors as $director){
            try{
                $directorModel = $this->findBy('tmdb_id', $director['tmdb_id']);
            }
            catch(ModelNotFoundException $e){
                $directorModel = null;
            }
            if(!$directorModel) {
                $directorModel = $this->save($director);
            }
            $directorsId[] = $directorModel->id;
        }

        return $directorsId;
    }
}