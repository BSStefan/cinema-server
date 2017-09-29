<?php

namespace App\Http\Crawlers;

use App\Exceptions\CrawlerException;

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
     * @return array
     */
    public function findCurrentProjections() : array
    {

        return [];
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