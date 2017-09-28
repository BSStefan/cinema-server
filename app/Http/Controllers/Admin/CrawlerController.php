<?php

namespace App\Http\Controllers\Admin;

use App\Http\Responses\JsonResponse;
use App\Repositories\CinemaRepository;
use App\Repositories\MovieRepository;
use App\Services\CrawlerService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    public function getCurrentInCinema(Request $request)
    {
        $validated = $this->validate($request, [
            'cinema_id' => 'required|integer'
        ]);
        $cinema = $this->cinemaRepository->find($validated['cinema_id']);
        $movies = $this->crawlerService->currentInCinema($cinema);

        return response()->json(new JsonResponse($movies));
    }
}
