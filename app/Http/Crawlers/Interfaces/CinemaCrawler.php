<?php

namespace App\Http\Crawlers\Interfaces;

interface CinemaCrawler
{
    public function getPageHtml(string $url) : CinemaCrawler;

    public function getDomDocument() : \DOMDocument;

    public function findCurrentMovies(string $url) : array;

    public function findSoonMovies(string $url) : array;

    public function findCurrentProjections(string $url) : array;

    public function addInHeader(string $param, string $value) : CinemaCrawler;
}