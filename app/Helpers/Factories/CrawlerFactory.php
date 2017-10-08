<?php

namespace App\Helpers\Factories;

use App\Http\Crawlers\Interfaces\CinemaCrawler;
use Illuminate\Container\Container as App;

class CrawlerFactory
{
    /**
     * @var App $app
     */
    private $app;

    public function __construct
    (
        App $app
    )
    {
        $this->app = $app;
    }

    /**
     * @param string $type
     * @return CinemaCrawler
     */
    public function make(string $type) : CinemaCrawler
    {
        switch($type) {
            case 'CinaplexxCrawler' :
                return $this->app->make('App\Http\Crawlers\CinaplexxCrawler');
            case 'TuckCrawler' :
                return $this->app->make('App\Http\Crawlers\TuckCrawler');
            case 'FontanaCrawler' :
                return $this->app->make('App\Http\Crawlers\FontanaCrawler');
            case 'RodaArenaCinaplexCrawler' :
                return $this->app->make('App\Http\Crawlers\RodaArenaCinaplexCrawler');
            default :
                return $this->app->make('App\Http\Crawlers\CinaplexxCrawler');
        }
    }
}