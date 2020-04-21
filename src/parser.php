<?php
require_once ("htmlCrawler.php");

namespace Test\Parser;
interface IParser
{
    public function parse($url);
}

class Parser implements IParser
{
    public $crawler;
    public IBasicParamEvent $onFind;

    function __construct(IHtmlCrawler $crawler)
    {
        $this->crawler = $crawler;
    }
    public function parse($url)
    {
        //$this->crawler->
    }
}

?>