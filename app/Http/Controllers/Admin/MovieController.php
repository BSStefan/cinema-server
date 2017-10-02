<?php

namespace App\Http\Controllers\Admin;

use App\Http\Responses\JsonResponse;
use App\Repositories\MovieRepository;
use App\Services\MovieService;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse as JsonIllResponse;

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

    public function postMovieFromTmdb(Request $request): JsonIllResponse
    {
        $validated = $this->validate($request, [
            'tmdb_id' => 'required|integer',
            'description' => 'string'
        ]);

        $movie = $this->tmdbService->findMovie($request->tmdb_id);

        $saved = $this->movieService->saveNewMovie($movie);

        return response()->json(new JsonResponse(['success' => $saved]));
    }
}
