<?php

namespace App\Http\Crawlers;

class RodaArenaCinaplexCrawler extends BasicCrawler
{
    /**
     * @param string $url
     * @return array
     */
    public function findCurrentMovies(string $url): array
    {
        $movies = [];
        $dom    = $this->getPageHtml($url)->getDomDocument();
        $divs   = $dom->getElementsByTagName('div');
        foreach($divs as $div){
            $classes = $div->getAttribute('class');
            if($classes == 'single_rep'){
                $movie                = [];
                $movie['title']       = $div->getElementsByTagName('a')[0]->nodeValue;
                $movie['start_date']  = '';
                $movie['description'] = '';
                $divChildren          = $div->getElementsByTagName('div');
                foreach($divChildren as $divChild){
                    if($divChild->getAttribute('id') == 'date'){
                        $movie['original_title'] = $divChild->nodeValue;
                    }
                }
                $movies[] = $movie;
            }
        }

        return $movies;
    }

    /**
     * @param string $url
     * @return array
     */
    public function findSoonMovies(string $url): array
    {
        $movies = [];
        $dom    = $this->getPageHtml($url)->getDomDocument();
        $divs   = $dom->getElementsByTagName('div');
        foreach($divs as $div){
            $classes = $div->getAttribute('class');
            if($classes == 'single_rep'){
                $movie          = [];
                $movie['title'] = $div->getElementsByTagName('a')[0]->nodeValue;
                $divChildren    = $div->getElementsByTagName('div');
                foreach($divChildren as $divChild){
                    if($divChild->getAttribute('id') == 'sati_najava'){
                        $date                = $divChild->nodeValue;
                        $movie['start_date'] = explode('/', $date)[2] . '-' . explode('/', $date)[1] . '-' . explode('/', $date)[0];
                    }
                    if($divChild->getAttribute('id') == 'date'){
                        $movie['original_title'] = $divChild->nodeValue;
                    }
                }
                $movie['description'] = '';
                $movies[]             = $movie;
            }
        }

        return $movies;
    }

    /**
     * @param string $url
     * @return array
     */
    public function findCurrentProjections(string $url): array
    {
        $projections = [];
        $urls   = $this->findLinks($url);
        foreach($urls as $url){
            $dom    = $this->getPageHtml($url)->getDomDocument();
            $divs   = $dom->getElementsByTagName('div');
            foreach($divs as $div){
                $classes = $div->getAttribute('class');
                if($classes == 'single_rep'){
                    $title = trim($div->getElementsByTagName('a')[0]->nodeValue);
                    $date = explode('/', $url)[4];
                    $divChildren    = $div->getElementsByTagName('div');
                    $originalTitle = '';
                    foreach($divChildren as $divChild){
                        $projection = [
                            'title' => $title,
                            'date'  => $date
                        ];
                        if($divChild->getAttribute('id') == 'date'){
                            $originalTitle = trim($divChild->nodeValue);
                        }
                        if($divChild->getAttribute('class') == 'projekcija') {
                            $projection['time'] = $divChild->firstChild->nodeValue;
                            $projection['original_title']= $originalTitle;
                            $projection['room'] = explode(' ',$divChild->firstChild->nextSibling->nextSibling->nodeValue)[1];
                            $projection['price'] = explode(' ', $divChild->firstChild->nextSibling->nodeValue)[0];
                            $projections[] = $projection;
                        }
                    }
                }
            }
        }

        return $projections;
    }

    /**
     * @param string $url
     * @return array
     */
    public function findLinks(string $url): array
    {
        $urls  = [];
        $dates = $this->getPageHtml($url)->getDomDocument()->getElementById('cal_items');
        $links = $dates->getElementsByTagName('a');
        foreach($links as $link){
            $urls[] = 'http://www.rodacineplex.com' . $link->getAttribute('href');
        }

        return $urls;
    }
}