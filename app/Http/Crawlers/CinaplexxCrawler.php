<?php

namespace App\Http\Crawlers;

use App\Exceptions\CrawlerException;
use Carbon\Carbon;

class CinaplexxCrawler extends BasicCrawler
{

    /**
     * @param string $url
     * @return array
     */
    public function findCurrentMovies(string $url): array
    {
        $movies    = [];
        $movieUrls = $this->findLinksForDetails($url);
        foreach($movieUrls as $movieUrl){
            try{
                $domDocument = $this->getPageHtml($movieUrl)->getDomDocument();
                $originalTitle = $this->findOriginalTitle($domDocument);
                $description = $this->findDescription($domDocument);
                $startDate     = $this->findStartDate($domDocument);
            }
            catch(CrawlerException $exception){
                $originalTitle = 'Error';
                $description   = 'Error';
                $startDate     = 'Error';
            }
            $movies[] = [
                'original_title' => $originalTitle,
                'title'          => $originalTitle,
                'description'    => $description,
                'start_date'     => $startDate
            ];
        }

        return $movies;
    }

    /**
     * @param string $url
     * @return array
     */
    public function findSoonMovies(string $url) : array
    {
        return $this->findCurrentMovies($url);
    }

    /**
     * @param string $url
     * @return array
     */
    public function findCurrentProjections(string $url) : array
    {
        $weekProjections = [];
        for($i = 0; $i < 7; $i++){
            $date = Carbon::now()->addDays($i)->toDateString();
            $url  = str_replace('*', $date, $url);
            $domDocument = $this->getPageHtml($url)->getDomDocument();
            $cinema      = null;
            $date        = null;
            $movies      = [];
            $selects     = $domDocument->getElementsByTagName('select');
            $divs        = $domDocument->getElementsByTagName('div');
            foreach($selects as $select){
                $name = $select->getAttribute('name');
                if($name == 'centerId'){
                    $options = $select->getElementsByTagName('option');
                    foreach($options as $option){
                        if($option->hasAttribute('selected')){
                            $cinema = $option->nodeValue;
                        }
                    }
                }
                if($name == 'date'){
                    $options = $select->getElementsByTagName('option');
                    foreach($options as $option){
                        if($option->hasAttribute('selected')){
                            $date = $option->getAttribute('value');
                        }
                    }
                }
            }
            foreach($divs as $div){
                $classes = $div->getAttribute('class');
                if(strpos($classes, 'overview-element') !== false){
                    $title = $div->getElementsByTagName('h2')[0]->nodeValue;
                    $movieTitle = '';
                    $childDivs  = $div->getElementsByTagName('div');
                    foreach($childDivs as $childDiv){
                        $childClasses = $childDiv->getAttribute('class');
                        if(strpos($childClasses, 'starBoxSmall') !== false){
                            $pTags      = $div->getElementsByTagName('p');
                            $movieTitle = $pTags[1]->nodeValue;
                        }
                        if(strpos($childClasses, 'start-times') !== false){
                            $aTags = $childDiv->getElementsByTagName('a');
                            foreach($aTags as $a){
                                $movieTime        = [
                                    'original_title'  => utf8_decode($movieTitle),
                                    'cinema' => $cinema,
                                    'date'   => $date
                                ];
                                $pTags            = $a->getElementsByTagName('p');
                                $movieTime['url'] = trim($a->getAttribute('href'));
                                foreach($pTags as $p){
                                    $class = $p->getAttribute('class');
                                    if(strpos($class, 'time-desc') !== false){
                                        $movieTime['time'] = trim($p->nodeValue);
                                    }
                                    else if(strpos($class, 'room-desc') !== false){
                                        $movieTime['room'] = trim($p->nodeValue);
                                    }
                                }
                                $movieTime['title'] = $title;
                                array_push($movies, $movieTime);
                            }
                        }
                    }
                }
            }
            $weekProjections     = array_merge($weekProjections, $movies);
        }
        return $weekProjections;
    }

    /**
     * @param string $url
     * @return array
     */
    private function findLinksForDetails(string $url): array
    {
        $urls    = [];
        $h2Nodes = $this->getPageHtml($url)->getDomDocument()->getElementsByTagName('h2');
        foreach($h2Nodes as $h2){
            if($h2->firstChild instanceof \DOMElement){
                if($h2->firstChild->tagName == 'a'){
                    array_push($urls, 'http:' . $h2->firstChild->getAttribute('href'));
                }
            }
        }

        return $urls;
    }

    /**
     * @param \DOMDocument $domDocument
     * @return string
     * @throws CrawlerException
     */
    private function findOriginalTitle(\DOMDocument $domDocument) : string
    {
        $tables = $domDocument->getElementsByTagName('table');
        foreach($tables as $table){
            $tr = $table->firstChild;
            foreach($tr->childNodes as $td){
                if($td instanceof \DOMElement && $td->nodeValue != 'Originalni naslov:'){
                    return $td->nodeValue;
                }
            }
        }
        throw new CrawlerException("Can't find title.", 1);
    }

    /**
     * @param \DOMDocument $domDocument
     * @return string
     * @throws CrawlerException
     */
    private function findDescription(\DOMDocument $domDocument) : string
    {
        $divs = $domDocument->getElementsByTagName('div');
        foreach($divs as $div){
            $class = $div->getAttribute('class');
            if(strpos($class,'two-columns') !== false){
                $p                = $div->getElementsByTagName('p');
                $movieDescription = $p[0]->nodeValue;
                return $movieDescription;
            }
        }
        throw new CrawlerException("Can't find description.", 1);
    }

    /**
     * @param \DOMDocument $domDocument
     * @return string
     * @throws CrawlerException
     */
    private function findStartDate(\DOMDocument $domDocument) : string
    {
        $tables = $domDocument->getElementsByTagName('table');
        foreach($tables as $table){
            $trs = $table->childNodes;
            return $trs[1]->childNodes[2]->nodeValue;
        }
        throw new CrawlerException("Can't find title.", 1);
    }
}