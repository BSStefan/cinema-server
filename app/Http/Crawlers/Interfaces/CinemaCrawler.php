<?php

namespace App\Http\Crawlers\Interfaces;

interface CinemaCrawler
{
    public function getPageHtml($url) : CinemaCrawler;

    public function findCurrentMovies($url) : array;

    public function findCurrentProjections() : array;
}