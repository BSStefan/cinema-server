<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TmdbExaption;
use App\Http\Responses\JsonResponse;
use App\Repositories\MovieRepository;
use App\Services\MovieService;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse as JsonIllResponse;
use Tmdb\Repository\SearchRepository;

class MovieController extends Controller
{
    /**
     * @var TmdbService $tmdbService
     */
    private $tmdbService;

    /**
     * @var MovieRepository $movieRepository
     */
    private $movieRepository;

    /**
     * @var MovieService $movieService
     */
    private $movieService;

    public function __construct
    (
        TmdbService $tmdbService,
        MovieRepository $movieRepository,
        MovieService $movieService
    )
    {
        $this->tmdbService     = $tmdbService;
        $this->movieRepository = $movieRepository;
        $this->movieService    = $movieService;
    }

    public function postMovieFromTmdb(Request $request, SearchRepository $searchRepository): JsonIllResponse
    {
        $validated = $this->validate($request, [
            'tmdb_id' => 'required_required_without_all:title|integer',
            'title'   => 'required_without_all:tmdb_id|string',
            'description' => 'string'
        ]);

        if(isset($validated['title'])){
            try{
                $tmdb_id = $this->tmdbService->searchMovie($validated['title'], $searchRepository);
            }
            catch(TmdbExaption $e){
                return response()->json(new JsonResponse(['success' => false], $e->getMessage(), 404), 404);
            }
        }
        else{
            $tmdb_id = $validated['$tmdb_id'];
        }

        $movie = $this->tmdbService->findMovie($tmdb_id);

        $saved = $this->movieService->saveNewMovie($movie);

        return response()->json(new JsonResponse(['success' => $saved]));
    }
}
