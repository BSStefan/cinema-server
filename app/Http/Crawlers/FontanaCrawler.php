<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 6.10.17.
 * Time: 09.58
 */

namespace App\Http\Crawlers;

class FontanaCrawler extends BasicCrawler
{

    public function findCurrentMovies(string $url): array
    {
        $movies = [];
        $urls = $this->findLinks($url);
        $this->addInHeader('X-Requested-With', 'XMLHttpRequest');
        foreach($urls as $url) {
            $movie = [];
            $dom = $this->getPageHtml($url)->getDomDocument();
            $movie['title'] = trim($dom->getElementsByTagName('h1')[0]->nodeValue);
            $uls = $dom->getElementsByTagName('ul');
            foreach($uls as $ul) {
                if($ul->getAttribute('class') == 'details-list'){
                    $lis = $ul->getElementsByTagName('li');
                    foreach($lis as $li){
                        if(explode(':',trim($li->nodeValue)[0] == 'Originalni naziv')) {
                            $movie['original_title'] = $li->getElementsByTagName('span')[0]->nodeValue;
                        }
                        if(explode(':', trim($li->nodeValue)[0] == 'Sinopsis')){
                            var_dump($li->nodeValue); exit;
                            $movie['description'] = $li->getElementsByTagName('span')[0]->nodeValue;
                        }
                    }
                }
            }
            $movie['start_date'] = '';
            dd($movie);
        }
        return [];
    }

    public function findSoonMovies(string $url): array
    {
        return [];
    }

    public function findCurrentProjections(string $url): array
    {
        return [];
    }

    private function findLinks(string $url) : array
    {
        $urls = [];
        $header = $this->getPageHtml($url)->getDomDocument()->getElementsByTagName('header')[0];
        $lis = $header->getElementsByTagName('li');
        foreach($lis as $li){
            $liClass = $li->getAttribute('class');
            if($liClass == 'scroll dropdown'){
                $a = $li->getElementsByTagName('a')[0];
                if($a->getAttribute('href') == '#repertoarfilmovi'){
                    $lisChildren = $li->getElementsByTagName('li');
                    foreach($lisChildren as $lisChild){
                        $aChild = $lisChild->getElementsByTagName('a')[0];
                        $id = explode('(',$aChild->getAttribute('onclick'))[1];
                        $id = substr($id, 0, strlen($id)-1);
                        $urls[] = 'http://www.bioskopfontana.rs/film.php?id='. $id .'&s=1';
                    }
                }
            }
        }

        return $urls;
    }
}