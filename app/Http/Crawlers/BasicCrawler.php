<?php

namespace App\Http\Crawlers;

use App\Http\Crawlers\Interfaces\CinemaCrawler;
use DOMDocument;

abstract class BasicCrawler implements CinemaCrawler
{
    /**
     * Default settings
     * @var array $options
     */
    protected $options = [
        'http' => [
            'method'  => 'GET',
            'headers' => [
                'User-Agent: Test/0.1'
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
        if($options) {
            $this->options = $options;
        }
        $this->context = stream_context_create($this->options);
        $this->DOMDocument = new DOMDocument();
    }

    /**
     * @param  string $url
     * @return CinemaCrawler
     */
    public function getPageHtml($url) : CinemaCrawler
    {
        @$this->DOMDocument->loadHTML(@file_get_contents($url, false, $this->context));
        return $this;
    }
}