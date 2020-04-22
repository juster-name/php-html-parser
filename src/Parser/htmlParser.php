<?php
namespace Test\Parser;
require_once ("event.php");
require_once ("crawler.php");

interface IParser
{
    public function parse($path);
    
}

interface IParseOptions
{
    public function getFindOptions() : array;
}

abstract class BaseParser implements IParser
{
    public array $findOptions;
    public BasicParamEvent $onFind;
    public BasicParamEvent $onError;
    public BasicParamEvent $onFileLoading;
    public BasicParamEvent $onFileLoaded;
    public BasicParamEvent $onStart;

    function __construct(array $options)
    {
        $this->options = $options;
    
        $this->onFind = new BasicParamEvent();
        $this->onError = new BasicParamEvent();
        $this->onFileLoading = new BasicParamEvent();
        $this->onFileLoaded = new BasicParamEvent();
        $this->onStart = new BasicParamEvent();       
    }
    abstract public function parse($path);
    public function getFindOptions() : array
    {
        return $this->findOptions;
    }
}

class HtmlParser extends BaseParser
{
    function __construct(IHtmlCrawler $crawler, array $findOptions)
    {
        parent::__construct($findOptions);
        $this->crawler = $crawler;
    }
    public function parse($url)
    {
        $this->crawler->loadHTMLFile($url);
        $this->onStart->invoke($url);
        
        while (empty($queue) === false) 
        {
            $url = array_pop($queue);
            $this->parsePage($this->findOptions);         
        }

    }

    private function parsePage($findOptions)
    {
        /*
        foreach ($findOptions as $tagName => $att)
        {
            //_log("Looping through <$tagName>");

            foreach($att as $attOpt)
            {
                parseAtt($tagName, $attOpt['name'], $attOpt['filter']);
            }

            try
            {
                $allATags = $document->getElementsByTagName("a");
            }
            catch(Exception $e)
            {
                array_push($error, "ERROR in getting <a> tag: " . $e);
                _log("ERROR in getting <a> tag (logged)");
            }
            
        }
        */
    }
}

?>