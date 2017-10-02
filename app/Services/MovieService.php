<?php

namespace App\Services;

use App\Models\Movie;
use App\Repositories\ActorRepository;
use App\Repositories\DirectorRepository;
use App\Repositories\GenreRepository;
use App\Repositories\MovieRepository;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\File;

class MovieService
{
    /**
     * @var MovieRepository
     */
    private $movieRepository;

    /**
     * @var DirectorRepository
     */
    private $directorRepository;

    /**
     * @var ActorRepository
     */
    private $actorRepository;

    /**
     * @var GenreRepository
     */
    private $genreRepository;

    public function __construct
    (
        MovieRepository $movieRepository,
        DirectorRepository $directorRepository,
        ActorRepository $actorRepository,
        GenreRepository $genreRepository
    )
    {
        $this->movieRepository    = $movieRepository;
        $this->actorRepository    = $actorRepository;
        $this->directorRepository = $directorRepository;
        $this->genreRepository    = $genreRepository;
    }

    /**
     * @param array $movie
     * @return bool
     */
    public function saveNewMovie(array $movie) : bool
    {
        $movie['movie']['image_url'] = $this->saveImageFromUrl($movie['movie']['image_url'], 'images/movies');
        $movieModel = $this->movieRepository->save($movie['movie']);

        $genres = [];
        foreach($movie['genres'] as $genre){
            $genreId = $this->genreRepository->findBy('name', $genre)->id;
            $genres[] = $genreId;
        }

        $movieModel->genres()->attach($genres);

        $actors = $this->actorRepository->saveMovieActors($movie['actors']);
        $movieModel->actors()->attach($actors);

        $directors = $this->directorRepository->saveMovieDirectors($movie['directors']);
        $movieModel->directors()->attach($directors);

        return $movieModel ? true : false;
    }

    /**
     * Save Image
     * @param string $url
     * @param string $path
     * @return string
     */
    public function saveImageFromUrl(string $url,string $path) : string
    {
        $extension = pathinfo($url,PATHINFO_EXTENSION);
        $fullName =  $path . '/' . md5(microtime()) . '.' . $extension;
        try{
            $image = Image::make($url)->encode('jpg', 60)->resize(800,1200);
            $image->save(public_path($fullName));
            $saved_image_uri = $image->dirname.'/'.$image->basename;
            $uploaded_thumbnail_image = Storage::putFileAs('public/', new File($saved_image_uri), $fullName);
            $image->destroy();
            unlink($saved_image_uri);
        }
        catch(\Exception $e){
            $fullName = 'images/default.jpg';
        }

        return $fullName;
    }
}