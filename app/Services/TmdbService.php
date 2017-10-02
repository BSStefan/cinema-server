<?php

namespace App\Services;

use App\Exceptions\TmdbExaption;
use Carbon\Carbon;
use Tmdb\Helper\ImageHelper;
use Tmdb\Model\AbstractModel;
use Tmdb\Model\Collection\People\Cast;
use Tmdb\Model\Collection\People\Crew;
use Tmdb\Model\Common\GenericCollection;
use Tmdb\Model\Movie;
use Tmdb\Model\Search\SearchQuery\MovieSearchQuery;
use Tmdb\Repository\MovieRepository;
use Tmdb\Repository\SearchRepository;

class TmdbService
{
    /**
     * @var MovieRepository
     */
    private $movieRepository;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    public function __construct
    (
        MovieRepository $movieRepository,
        ImageHelper $imageHelper
    )
    {
        $this->movieRepository = $movieRepository;
        $this->imageHelper     = $imageHelper;
    }

    /**
     * @param string $movie
     * @param SearchRepository $searchRepository
     * @return int
     * @throws TmdbExaption
     */
    public function searchMovie(string $movie, SearchRepository $searchRepository) : int
    {
        $options = new MovieSearchQuery();
        $options->includeAdult(false)->year(Carbon::now()->year);
        $movies = $searchRepository->searchMovie($movie, $options);

        foreach($movies as $movieOne){
            if($movieOne->getOriginalTitle() == $movie){
                return $movieOne->getId();
            }
        }
        throw new TmdbExaption('Movie not found', 1);
    }

    /**
     * @param int $tmdb_id
     * @return array
     */
    public function findMovie(int $tmdb_id) : array
    {
        return $this->formatMovie($this->movieRepository->load($tmdb_id));
    }

    /**
     * @param AbstractModel $movie
     * @return array
     */
    private function formatMovie(AbstractModel $movie) : array
    {
        $newMovie                            = [];
        $newMovie['movie']['tmdb_id']        = $movie->getId();
        $newMovie['movie']['homepage']       = $movie->getHomepage();
        $newMovie['movie']['title']          = $movie->getTitle();
        $newMovie['movie']['original_title'] = $movie->getOriginalTitle();
        $newMovie['movie']['language']       = $movie->getOriginalLanguage();
        $newMovie['movie']['release_day']    = $movie->getReleaseDate()->format('Y-m-d H:i:s');
        $newMovie['movie']['runtime']        = $movie->getRuntime();
        $newMovie['movie']['budget']         = $movie->getBudget();
        $newMovie['movie']['description']    = $movie->getOverview();
        $newMovie['genres']                  = $this->formatSimpleCollection($movie->getGenres());
        $newMovie['movie']['image_url']      = 'http:' . $this->imageHelper->getUrl($movie->getPosterImage());
        $newMovie['actors']                  = $this->formatCast($movie->getCredits()->getCast());
        $newMovie['directors']               = $this->formatCrew($movie->getCredits()->getCrew());

        return $newMovie;
    }

    /**
     * @param Cast $cast
     * @return array
     */
    private function formatCast(Cast $cast) : array
    {
        $newCast = [];
        $i       = 0;
        foreach($cast as $person){
            if($i < 5){
                array_push($newCast, ['tmdb_id' => $person->getId(), 'name' => $person->getName()]);
                $i++;
            }
            else{
                break;
            }
        }
        return $newCast;
    }

    /**
     * @param Crew $crew
     * @return array
     */
    private function formatCrew(Crew $crew) : array
    {
        $directors = [];
        foreach($crew as $person){
            if($person->getDepartment() == 'Directing'){
                $directors[] = ['tmdb_id' => $person->getId(), 'name' => $person->getName()];
            }
        }
        return $directors;
    }

    /**
     * @param GenericCollection $list
     * @return array
     */
    private function formatSimpleCollection(GenericCollection $list)
    {
        $newList = [];
        foreach($list as $item){
            array_push($newList, $item->getName());
        }
        return $newList;
    }

}