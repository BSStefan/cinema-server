<?php

namespace App\Http\Crawlers;

use Carbon\Carbon;

class FontanaCrawler extends BasicCrawler
{
    /**
     * @param string $url
     * @return array
     */
    public function findCurrentMovies(string $url): array
    {
        $movies = [];
        $urls   = $this->findLinks($url);
        $this->addInHeader('X-Requested-With', 'XMLHttpRequest');
        foreach($urls as $url){
            $movie          = [];
            $dom            = $this->getPageHtml($url)->getDomDocument();
            $movie['title'] = trim($dom->getElementsByTagName('h1')[0]->nodeValue);
            $lis            = $dom->getElementsByTagName('li');
            foreach($lis as $li){
                $node = explode(':', trim($li->nodeValue));
                if($node[0] !== '' && $node[0] == 'Originalni naziv'){
                    $movie['original_title'] = trim($node[1]);
                }
                if($node[0] !== '' && $node[0] == 'Sinopsis'){
                    $movie['description'] = trim($node[1]);
                }
            }
            $movie['start_date'] = '';
            $movies[]            = $movie;
        }

        return $movies;
    }

    /**
     * @param string $url
     * @return array
     */
    public function findSoonMovies(string $url): array
    {
        return [];
    }

    /**
     * @param string $url
     * @return array
     */
    public function findCurrentProjections(string $url): array
    {
        $projections = [];
        $urls        = $this->findLinks($url);
        $this->addInHeader('X-Requested-With', 'XMLHttpRequest');
        foreach($urls as $url){
            $dom            = $this->getPageHtml($url)->getDomDocument();
            $divs           = $dom->getElementsByTagName('div');
            $original_title = '';
            foreach($divs as $div){
                $classes = explode(' ', $div->getAttribute('class'));
                if(in_array('pregled-details', $classes)){
                    $lis = $div->getElementsByTagName('li');
                    foreach($lis as $li){
                        $node = explode(':', trim($li->nodeValue));
                        if($node[0] !== '' && $node[0] == 'Originalni naziv'){
                            $original_title = trim($node[1]);
                        }
                    }
                }
            }
            foreach($divs as $div){
                $classes = explode(' ', $div->getAttribute('class'));
                if(in_array('projekcija', $classes) && !in_array('obsolete', $classes)){
                    $projection                   = [];
                    $firstRowTime                 = $div->firstChild->firstChild->firstChild;
                    $secondRowDate                = $div->lastChild->lastChild->lastChild;
                    $projection['time']           = $firstRowTime->nodeValue;
                    $date                         = substr($secondRowDate->nodeValue, 0, strlen($secondRowDate->nodeValue) - 1);
                    $date                         = Carbon::now()->year . '-' . explode('.', $date)[1] . '-' . explode('.', $date)[0];
                    $projection['date']           = $date;
                    $projection['room']           = '';
                    $projection['title']          = trim($dom->getElementsByTagName('h1')[0]->nodeValue);
                    $projection['original_title'] = $original_title;
                    $projections[]                = $projection;
                }
            }
        }

        return $projections;
    }

    /**
     * @param string $url
     * @return array
     */
    private function findLinks(string $url): array
    {
        $urls   = [];
        $header = $this->getPageHtml($url)->getDomDocument()->getElementsByTagName('header')[0];
        $lis    = $header->getElementsByTagName('li');
        foreach($lis as $li){
            $liClass = $li->getAttribute('class');
            if($liClass == 'scroll dropdown'){
                $a = $li->getElementsByTagName('a')[0];
                if($a->getAttribute('href') == '#repertoarfilmovi'){
                    $lisChildren = $li->getElementsByTagName('li');
                    foreach($lisChildren as $lisChild){
                        $aChild = $lisChild->getElementsByTagName('a')[0];
                        $id     = explode('(', $aChild->getAttribute('onclick'))[1];
                        $id     = substr($id, 0, strlen($id) - 1);
                        $urls[] = 'http://www.bioskopfontana.rs/film.php?id=' . $id . '&s=1';
                    }
                }
            }
        }

        return $urls;
    }
}