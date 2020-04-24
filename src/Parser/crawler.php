<?php
namespace Test\Parser;

interface ICrawler
{
    public function load($param) : bool;
    public function crawl($param);
}

class HtmlCrawler implements ICrawler
{
    private $doc;
    public bool $isNoWarnings;

    function __construct($isNoWarnings = true)
    {
        $this->isNoWarnings = $isNoWarnings;
        $this->doc = new \DOMDocument();
    }

    public function load($url = null) : bool
    {
        if (empty($this->url))
        {
            $this->url = $url;
        }

        if (empty($this->url))
        {
            throw new \Exception("URL must not be empty while loading HTML file");
        }

        return $this->isNoWarnings ? @$this->doc->loadHTMLFile($url) : $this->doc->loadHTMLFile($url);;
    }
   
    public function crawl($tagName) : \DOMNodeList
    {
        return $this->doc->getElementsByTagName($tagName);
    }
}

?>