<?php
namespace Test\Parser;

require_once ("event.php");
require_once ("htmlCrawler.php");


interface IParser
{
    public function parse($url);
}

class Parser implements IParser
{
    public $crawler;
    public IBasicParamEvent $onFind;
    public IBasicParamEvent $onError;
    public IBasicParamEvent $onFileLoading;
    public IBasicParamEvent $onFileLoaded;
    public IBasicParamEvent $onStart;

    function __construct(IHtmlCrawler $crawler)
    {
        $this->crawler = $crawler;

        $this->onFind = new BasicParamEvent();
        $this->onError = new BasicParamEvent();
        $this->onFileLoading = new BasicParamEvent();
        $this->onFileLoaded = new BasicParamEvent();
        $this->onStart = new BasicParamEvent();
    }
    public function parse($url)
    {
        //$this->crawler->loadHTMLFile($url);
        $this->onStart->invoke($url);
        /*
        while (empty($queue) === false) 
        {
            $url = array_pop($queue);
            parse(
                ['a'=> 
                    ['href', 'findHref'],
                ]);
    
            
        }
        */
    }
}
$p = new Parser(new HtmlCrawler());
$p->onStart->add(new TestFindAction());
$p->parse("https://google.com");
?>