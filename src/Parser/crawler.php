<?php
namespace Test\Parser;

interface ICrawler
{
    public function load($param);
}

interface IHtmlCrawler extends ICrawler
{
    public function loadHTMLFile($url) : bool;
    public function getElementsByTagName($tagName);
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
    public function load($param)
    {
        return @$this->doc->loadHTMLFile($param);
    }

    public function loadHTMLFile($url = null) : bool
    {
        if (empty($this->url))
        {
            $this->url = $url;
        }

        if (empty($this->url))
        {
            throw new \Exception("URL must not be empty while loading HTML file");
        }

        return $this->load($url);
    }
   
    public function getElementsByTagName($tagName) : \DOMNodeList
    {
        return $this->doc->getElementsByTagName($tagName);
    }
}

?>