<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 3.10.17.
 * Time: 11.04
 */

namespace App\Http\Crawlers;

use Carbon\Carbon;

class TuckCrawler extends BasicCrawler
{

    /**
     * @param string $url
     * @return array
     */
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

    /**
     * @param string $url
     * @return array
     */
    public function findSoonMovies(string $url): array
    {
        $movies = [];
        $date   = Carbon::now();
        $urls   = [$url . '&year=' . $date->year . '&month=' . $date->month, $url . '&year=' . $date->year . '&month=' . ($date->month + 1)];
        foreach($urls as $urlLink){
            $domDocument = $this->getPageHtml($urlLink)->getDomDocument();
            $movieListLi = $domDocument->getElementById('movie-list')->getElementsByTagName('li');
            foreach($movieListLi as $movieLi){
                $class = $movieLi->getAttribute('class');
                if($class == 'details'){
                    $movie          = [
                        'title'          => '',
                        'original_title' => '',
                        'description'    => '',
                        'start_date'     => ''
                    ];
                    $movie['title'] = $movieLi->getElementsByTagName('a')[1]->nodeValue;
                    $divs           = $movieLi->getElementsByTagName('div');
                    foreach($divs as $div){
                        if($div->getAttribute('class') == 'original'){
                            $movie['original_title'] = $div->nodeValue;
                        }
                    }
                    $spans = $movieLi->getElementsByTagName('span');
                    foreach($spans as $span){
                        if($span->nodeValue == 'Start:'){
                            $movie['start_date'] = $this->formatDate($span->nextSibling->nodeValue);
                        }
                    }
                    $movies[] = $movie;
                }
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
                    $dateTime = $this->formatDateAndTime($li->nodeValue);
                    $projection['date'] = $dateTime['date'];
                    $projection['time'] = $dateTime['time'];
                    $projections[] = $projection;
                }
            }
        }

        return $projections;
    }

    /**
     * Return array with date and time formatted value
     * @param string $value
     * @return array
     */
    private function formatDateAndTime(string $value) : array {
        $timeDate = explode(' ', explode(', ', $value)[1]);
        $month = $this->getMonth($timeDate[1]);
        $date = '20' . $timeDate[2] .'-'. $month .'-'. $timeDate[0];
        $timeArray = explode(':', $timeDate[3]);
        $time = $timeArray[0] . ':' . $timeArray[1];

        return [
          'time' => $time,
          'date' => $date
        ];
    }

    /**
     * Return formatted date
     * @param string $date
     * @return string
     */
    public function formatDate(string $date) : string
    {
        $arrayDate = explode(' ', $date);
        $day = substr($arrayDate[1], 0, 2);
        $year = substr($arrayDate[3],0,4);
        $month = $this->getMonth($arrayDate[2]);

        return $year . '-' . $month . '-' . $day;
    }

    /**
     * Format month
     * @param string $monthRS
     * @return string
     */
    private function getMonth(string $monthRS) : string
    {
        switch($monthRS) {
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

        return $month;
    }

    /**
     * Find links for movies
     * @param string $url
     * @return array
     */
    private function findLinks(string $url): array
    {
        $divs  = $this->getPageHtml($url)->getDomDocument()
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