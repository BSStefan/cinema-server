<?php

namespace App\Services;

use App\Repositories\MovieRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrawlerService
{
    /**
     * @var App $app
     */
    private $app;

    /**
     * @var MovieRepository $movieRepository
     */
    protected $movieRepository;

    public function __construct(
        App $app,
        MovieRepository $movieRepository
    )
    {
        $this->app             = $app;
        $this->movieRepository = $movieRepository;
    }

    public function currentInCinema(Model $cinema) : array
    {
        $cinemaCrawler = $this->app->make($cinema->crawler);
        $movies = $cinemaCrawler->findCurrentMovies($cinema->page_url);
        for($i=0; $i<count($movies); $i++){
            try{
                $movieModel = $this->movieRepository->findBy('original_title', $movies[$i]['original_title']);
                $movies[$i]['in_db'] = true;
            }
            catch(ModelNotFoundException $exception){
                $movies[$i]['in_db'] = false;
            }
        }

        return $movies;
    }
}