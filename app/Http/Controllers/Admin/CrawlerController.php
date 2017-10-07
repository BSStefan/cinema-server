<?php

namespace App\Http\Controllers\Admin;

use App\Http\Responses\JsonResponse;
use App\Repositories\CinemaRepository;
use App\Repositories\MovieRepository;
use App\Services\CrawlerService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse as JsonIllResponse;

class CrawlerController extends Controller
{
    protected $cinemaRepository;
    protected $crawlerService;
    protected $movieRepository;

    public function __construct
    (
        CinemaRepository $cinemaRepository,
        CrawlerService $crawlerService,
        MovieRepository $movieRepository
    )
    {
        $this->cinemaRepository = $cinemaRepository;
        $this->crawlerService   = $crawlerService;
        $this->movieRepository  = $movieRepository;
    }

    /**
     * @param Request $request
     * @return JsonIllResponse
     */
    public function getCurrentInCinema(Request $request) : JsonIllResponse
    {
        $validated = $this->validate($request, [
            'cinema_id' => 'required|integer'
        ]);
        $cinema = $this->cinemaRepository->find($validated['cinema_id']);
        $movies = $this->crawlerService->currentInCinema($cinema);

        return response()->json(new JsonResponse($movies));
    }

    /**
     * @param Request $request
     * @return JsonIllResponse
     */
    public function getSoonInCinema(Request $request) : JsonIllResponse
    {
        $validated = $this->validate($request, [
            'cinema_id' => 'required|integer'
        ]);
        $cinema = $this->cinemaRepository->find($validated['cinema_id']);
        $movies = $this->crawlerService->soonInCinema($cinema);

        return response()->json(new JsonResponse($movies));
    }

    /**
     * @param Request $request
     * @return JsonIllResponse
     */
    public function postProjections(Request $request): JsonIllResponse
    {
        //TODO save projections
        $validated = $this->validate($request, [
            'cinema_id' => 'required|integer'
        ]);
        $cinema          = $this->cinemaRepository->find($validated['cinema_id']);
        $projections     = $this->crawlerService->findAllProjections($cinema);
        //$savedProjections = $this->movieService->saveProjections($projections);

        return response()->json(new JsonResponse($projections));
    }
}
