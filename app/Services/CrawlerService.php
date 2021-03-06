<?php

namespace App\Services;

use App\Helpers\Factories\CrawlerFactory;
use App\Http\Crawlers\Interfaces\CinemaCrawler;
use App\Models\Cinema;
use App\Repositories\MovieRepository;
use Carbon\Carbon;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Admin\TmdbRepository;

class CrawlerService
{
    /**
     * @var CrawlerFactory $crawlerFactory
     */
    protected $crawlerFactory;

    /**
     * @var MovieRepository $movieRepository
     */
    protected $movieRepository;

    /**
     * @var TmdbRepository $movieRepository
     */
    protected $tmdbRepository;

    public function __construct(
        MovieRepository $movieRepository,
        TmdbRepository $tmdbRepository,
        CrawlerFactory $crawlerFactory
    )
    {
        $this->crawlerFactory  = $crawlerFactory;
        $this->movieRepository = $movieRepository;
        $this->tmdbRepository  = $tmdbRepository;
    }

    /**
     * @param Cinema $cinema
     * @return array
     */
    public function currentInCinema(Cinema $cinema) : array
    {
        $cinemaCrawler = $this->crawlerFactory->make($cinema->crawler);
        $movies = $cinemaCrawler->findCurrentMovies($cinema->page_url);
        for($i=0; $i<count($movies); $i++){
            $movies[$i]['in_db'] = $this->checkIfMovieExists($movies[$i]['original_title']);
        }

        return $movies;
    }

    public function currentInAllCinemas(Collection $cinemas) : array
    {
        $allMovies = [];
        foreach($cinemas as $cinema){
            $cinemaCrawler = $this->crawlerFactory->make($cinema->crawler);
            $allMovies[$cinema->id]   = $cinemaCrawler->findCurrentMovies($cinema->page_url);
        }

        return $allMovies;
    }

    /**
     * @param Cinema $cinema
     * @return array
     */
    public function soonInCinema(Cinema $cinema) : array
    {
        $cinemaCrawler = $this->crawlerFactory->make($cinema->crawler);
        $movies = $cinemaCrawler->findSoonMovies($cinema->soon_url);
        for($i=0; $i<count($movies); $i++){
            $movies[$i]['in_db'] = $this->checkIfMovieExists($movies[$i]['original_title']);
        }

        return $movies;
    }

    /**
     * @param Cinema $cinema
     * @return array
     */
    public function findAllProjections(Cinema $cinema) : array
    {
        $cinemaCrawler = $this->crawlerFactory->make($cinema->crawler);

        return $cinemaCrawler->findCurrentProjections($cinema->crawler_link);

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