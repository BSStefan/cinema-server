<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 2.10.17.
 * Time: 11.28
 */

namespace App\Repositories;

use App\Models\Actor;
use App\Repositories\Eloquent\BasicRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ActorRepository extends BasicRepository
{
    protected $modelClass = Actor::class;

    public function saveMovieActors(array $actors) : array
    {
        $actorsId = [];
        foreach($actors as $actor){
            try{
                $actorModel = $this->findBy('tmdb_id', $actor['tmdb_id']);
            }
            catch(ModelNotFoundException $e){
                $actorModel = null;
            }
            if(!$actorModel) {
                $actorModel = $this->save($actor);
            }
            $actorsId[] = $actorModel->id;
        }

        return $actorsId;
    }
}