<?php

namespace App\Services;

use App\Models\Cinema;
use App\Repositories\MovieRepository;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Admin\TmdbRepository;

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

    /**
     * @var TmdbRepository $movieRepository
     */
    protected $tmdbRepository;

    public function __construct(
        App $app,
        MovieRepository $movieRepository,
        TmdbRepository $tmdbRepository
    )
    {
        $this->app             = $app;
        $this->movieRepository = $movieRepository;
        $this->tmdbRepository  = $tmdbRepository;
    }

    /**
     * @param Cinema $cinema
     * @return array
     */
    public function currentInCinema(Cinema $cinema) : array
    {
        $cinemaCrawler = $this->app->make($cinema->crawler);
        $movies = $cinemaCrawler->findCurrentMovies($cinema->page_url);
        for($i=0; $i<count($movies); $i++){
            $movies[$i]['in_db'] = $this->checkIfMovieExists($movies[$i]['original_title']);
        }

        return $movies;
    }

    /**
     * @param Cinema $cinema
     * @return array
     */
    public function soonInCinema(Cinema $cinema) : array
    {
        $cinemaCrawler = $this->app->make($cinema->crawler);
        $movies = $cinemaCrawler->findSoonMovies($cinema->soon_url);
        for($i=0; $i<count($movies); $i++){
            $movies[$i]['in_db'] = $this->checkIfMovieExists($movies[$i]['original_title']);
        }

        return $movies;
    }

    public function saveAllProjections($cinema)
    {
        $cinemaCrawler = $this->app->make($cinema->crawler);
        $moviesInCinema = $this->movieRepository->findWhere('in_cinema', true);

        return true;
    }

    /**
     * @param string $originalTitle
     * @return bool
     */
    private function checkIfMovieExists(string $originalTitle) : bool
    {
        try{
            $this->movieRepository->findBy('original_title', $originalTitle);
            return true;
        }
        catch(ModelNotFoundException $exception){
            return false;
        }
    }
}