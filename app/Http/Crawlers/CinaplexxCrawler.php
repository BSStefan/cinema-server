<?php

namespace App\Http\Crawlers;

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
            $tables = $this->getPageHtml($movieUrl)
                ->getDomDocument()
                ->getElementsByTagName('table');
            foreach($tables as $table){
                $tr = $table->firstChild;
                foreach($tr->childNodes as $td){
                    if($td instanceof \DOMElement && $td->nodeValue != 'Originalni naslov:'){
                        array_push($movies, ['original_title' => $td->nodeValue]);
                    }
                }
            }
        }

        return $movies;
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
}