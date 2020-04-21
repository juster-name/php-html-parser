<?php
namespace Test\Parser;

use Exception;

interface IHtmlCrawler
{
    public function loadHTMLFile($url);
    public function getTagsByName($tagName) : \Traversable;
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

    public function loadHTMLFile($url)
    {
        if (empty($url))
        {
            throw new Exception("URL must not be empty while loading HTML file");
        }

        return @$this->doc->loadHTMLFile($url);
    }
   
    public function getTagsByName($tagName)
    {
        $tags = [];
        foreach ($this->doc->getElementsByTagName($tagName) as $el)
        {
            array_push($tags, new Tag($el));
        }

        return $tags;
    }
}

?>