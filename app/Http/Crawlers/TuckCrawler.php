<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 3.10.17.
 * Time: 11.04
 */

namespace App\Http\Crawlers;

class TuckCrawler extends BasicCrawler
{

    public function findCurrentMovies(string $url): array
    {
        $links = $this->findLinks($url);
        $movies = [];
        foreach($links as $link){
            $movie = [];
            $domDocument = $this->getPageHtml($link)->getDomDocument();
            $divs = $domDocument->getElementsByTagName('div');
            foreach($divs as $div){
                $classes = $div->getAttribute('class');
                if($classes == 'description'){
                    $h1 = $div->getElementsByTagName('h1')[0];
                    $movie['title'] = $h1->nodeValue;
                }
                if($classes == 'original'){
                    $movie['original_title'] = $div->nodeValue;
                }
                $movie['description'] = '';
                $movie['start_date'] = '';
            }
            $movies[] = $movie;
        }

        return $movies;
    }

    public function findSoonMovies(string $url): array
    {
        // TODO: Implement findSoonMovies() method.
    }

    public function findCurrentProjections(string $url): array
    {
        $links       = $this->findLinks($url);
        $projections = [];
        foreach($links as $link){
            $projection  = [];
            $domDocument = $this->getPageHtml($link)->getDomDocument();
            $divs        = $domDocument->getElementsByTagName('div');
            foreach($divs as $div){
                $classes = $div->getAttribute('class');
                if($classes == 'description'){
                    $h1 = $div->getElementsByTagName('h1')[0];
                    $projection['title'] = $h1->nodeValue;
                }
                if($classes == 'original'){
                    $projection['original_title']  = $div->nodeValue;
                    $projection['cinema'] = 'Tuckwood';
                    $projection['url']    = '';
                }
            }
            $tuckwood = $domDocument->getElementById('tuckwood');
            $lis = $tuckwood->getElementsByTagName('li');
            foreach($lis as $li) {
                $classes = $li->getAttribute('class');
                if($classes == 'details') {
                    $projection['room'] = explode(' - ', $li->nodeValue)[1];
                }
                if($classes == 'repertory') {
                    $dateTime = $this->formateDateAndTime($li->nodeValue);
                    $projection['date'] = $dateTime['date'];
                    $projection['time'] = $dateTime['time'];
                    $projections[] = $projection;
                }
            }
        }

        return $projections;
    }

    private function formateDateAndTime(string $value) : array {
        $timeDate = explode(' ', explode(', ', $value)[1]);
        switch($timeDate[1]) {
            case 'Januar':
                $month = '01';
            break;
            case 'Februar':
                $month = '02';
            break;
            case 'Mart':
                $month = '03';
            break;
            case 'April':
                $month = '04';
            break;
            case 'Maj':
                $month = '05';
            break;
            case 'Jun':
                $month = '06';
            break;
            case 'Jul':
                $month = '07';
            break;
            case 'Avgust':
                $month = '08';
            break;
            case 'Septembar':
                $month = '09';
            break;
            case 'Oktobar':
                $month = '10';
            break;
            case 'Novembar':
                $month = '11';
            break;
            case 'Decembar':
                $month = '12';
            break;
            default:
                $month = '00';
        }
        $date = '20' . $timeDate[2] .'-'. $month .'-'. $timeDate[0];
        $timeArray = explode(':', $timeDate[3]);
        $time = $timeArray[0] . ':' . $timeArray[1];

        return [
          'time' => $time,
          'date' => $date
        ];
    }

    private function findLinks(string $url): array
    {
        $divs = $this->getPageHtml($url)->getDomDocument()
            ->getElementsByTagName('div');
        $links = [];
        foreach($divs as $div){
            $classes = $div->getAttribute('class');
            if($classes == 'description'){
                $aTags = $div->getElementsByTagName('a');
                foreach($aTags as $a){
                    if(!in_array('http://tuck.rs' . $a->getAttribute('href'), $links)){
                        $links[] = 'http://tuck.rs' . $a->getAttribute('href');
                    }
                }
            }
        }
        return $links;
    }
}