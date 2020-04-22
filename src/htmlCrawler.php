<?php
namespace Test\Parser;

use Exception;

interface IHtmlCrawler
{
    public function loadHTMLFile($url);
    public function getTagsByName($tagName);
}

class HtmlCrawler implements IHtmlCrawler
{
    public $url;
    private $doc;

    function __construct($url = null)
    {
        $this->doc = new \DOMDocument();
        $this->url = $url;
    }
    public function loadHTMLFile($url = null)
    {
        if (empty($this->url))
        {
            $this->url = $url;
        }

        if (empty($this->url))
        {
            throw new Exception("URL must not be empty while loading HTML file");
        }

        return @$this->doc->loadHTMLFile($url);
    }
   
    public function getTagsByName($tagName)
    {
        return $this->doc->getElementsByTagName($tagName);
    }
}

?>