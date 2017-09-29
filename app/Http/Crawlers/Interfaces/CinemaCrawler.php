<?php

namespace App\Http\Crawlers\Interfaces;

interface CinemaCrawler
{
    public function getPageHtml(string $url) : CinemaCrawler;

    public function findCurrentMovies(string $url) : array;

    public function findCurrentProjections() : array;

    public function getDomDocument() : \DOMDocument;

    public function findSoonMovies(string $url) : array;
}