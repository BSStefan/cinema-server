<?php

namespace App\Http\Crawlers;

use App\Http\Crawlers\Interfaces\CinemaCrawler;
use DOMDocument;

abstract class BasicCrawler implements CinemaCrawler
{
    /**
     * Default settings
     *
     * @var array $options
     */
    protected $options
        = [
            'http' => [
                'method'  => 'GET',
                'headers' => [
                    'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/61.0.3163.79 Chrome/61.0.3163.79 Safari/537.36'
                ]
            ]
        ];

    /**
     * @var resource $context
     */
    protected $context;

    /**
     * @var DOMDocument $DOMDocument
     */
    protected $DOMDocument;

    public function __construct(array $options = null)
    {
        if($options){
            $this->options = $options;
        }
        $this->context     = stream_context_create($this->options);
        $this->DOMDocument = new DOMDocument();
    }

    /**
     * @param  string $url
     * @return CinemaCrawler
     */
    public function getPageHtml(string $url): CinemaCrawler
    {
        @$this->DOMDocument->loadHTML(@file_get_contents($url, false, $this->context));

        return $this;
    }

    /**
     * @return DOMDocument
     */
    public function getDomDocument(): DOMDocument
    {
        return $this->DOMDocument;
    }

    /**
     * @param string $param
     * @param string $value
     * @return CinemaCrawler
     */
    public function addInHeader(string $param, string $value): CinemaCrawler
    {
        $this->options['http']['headers'][] = $param . ': ' . $value;

        return $this;
    }
}