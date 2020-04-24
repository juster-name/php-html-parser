<?php
namespace Test\Parser;

use DOMNodeList;

interface ICrawler
{
    public function load($param) : bool;
    public function crawl($param);
    public function scrapAll($crawlData, $param) : array;
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

        return $this->isNoWarnings ? @$this->doc->loadHTMLFile($url) : $this->doc->loadHTMLFile($url);
    }
   
    public function crawl($tagName) : \DOMNodeList
    {
        return $this->doc->getElementsByTagName($tagName);
    }
    public function scrapAll($crawlData, $attrName) : array
    {
        $nodeValues = [];
        foreach($crawlData as $element)
        {
            array_push($nodeValues, $element->getAttributeNode($attrName)->nodeValue);
        }
        return $nodeValues;
    }
}

class RegexCrawler implements ICrawler
{
    private $doc;
    public bool $isNoWarnings;
    private $context;

    function __construct($isNoWarnings = true)
    {
        $this->isNoWarnings = $isNoWarnings;
        $this->context = stream_context_create(array(
            'http'=>array(
              'method'=>"GET",
              'header'=> "Content-type: application/x-www-form-urlencoded",
              'user_agent'=> "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36"
            )
          ));

        //$this->doc = new \DOMDocument();
    }

    public function load($url = null) : bool
    {
        if (empty($url))
        {
            throw new \Exception("URL must not be empty while loading HTML file");
        }
        
        //$doc = fopen('https://url', 'r', false, $context);
        return $this->doc = file_get_contents($url, false, $this->context);
        //return $this->isNoWarnings ? @$this->doc->loadHTMLFile($url) : $this->doc->loadHTMLFile($url);
    }
   
    public function crawl($tagName)
    {
        file_put_contents(dirname(__FILE__) . "/steam.txt", $this->doc);
        $matches = [];
        preg_match_all('/<'.$tagName.'[^>]*>(.*?)<\/'.$tagName.'>/si', $this->doc, $matches);
        $arr = $this->scrapAll($matches[0], 'href');
        return $matches[0];
    }
    public function scrapAll($crawlData, $param) : array
    {
        $nodeValues = [];
        $i = 0;
        foreach($crawlData as $element)
        {
            $tmpArr = [];
            preg_match_all('/('.$param.'+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/', $element, $tmpArr);
            $nodeValues[$i] = $tmpArr[2][0];
            ++$i;
        }
        return $nodeValues;
    }
}
?>