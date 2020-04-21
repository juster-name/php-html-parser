<?php
namespace Test\Parser;

use Exception;

interface IHtmlCrawler
{
    public function getElementsByTagName($tagName, $url);
}

class HtmlCrawler implements IHtmlCrawler
{
    public $url;
    private $doc;

    function __construct($url = null)
    {
        $this->lazyInit($url);
    }

    public function getElementsByTagName($tagName, $url = null)
    {
        if ($this->lazyLoad($url) === false)
        {
            return false;
        }

        return $this->doc->getElementsByTagName($tagName);
    }

    private function lazyLoad($url, $noWarning = true)
    {
        $this->lazyInit($url);

        if (empty($url))
        {
            throw new Exception("URL must not be empty while loading HTML file");
        }

        return $noWarning ? @$this->doc->loadHTMLFile($url) : $this->doc->loadHTMLFile($url);
    }

    private function lazyInit($url)
    {
        if (!empty($url))
        {
            $this->url = $url;           
        }
        if (empty($this->doc) && !empty($this->url))
        {
            $this->doc = new \DOMDocument();
        }
    }
}

?>