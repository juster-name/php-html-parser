<?php
namespace Test\Parser;

require_once ("event.php");
require_once ("crawler.php");

interface IParser
{
    public function parse($path);  
    public function getPath();
    public function getOptions() : array;
    public function getSettings() : int;
}

abstract class ParserSettings
{
    public const Recursive = 0b0001;
    public const GoExternal = 0b0010;
}

abstract class BaseHtmlParser implements IParser
{
    public array $findOptions;
    public int $settings;
    public BasicParamEvent $onLog;
    public BasicParamEvent $onFind;
    public BasicParamEvent $onFilterSuccess;
    public BasicParamEvent $onFilterFail;
    public BasicParamEvent $onError;
    public BasicParamEvent $onFileLoading;
    public BasicParamEvent $onFileLoaded;
    public BasicParamEvent $onStart;
    public BasicParamEvent $onEnd;
    
    function __construct(array $findOptions, int $settings)
    {
        $this->findOptions = $findOptions;
        $this->settings = $settings;

        $this->onFind = new BasicParamEvent();
        $this->onError = new BasicParamEvent();
        $this->onFileLoading = new BasicParamEvent();
        $this->onFileLoaded = new BasicParamEvent();
        $this->onStart = new BasicParamEvent(); 
        $this->onEnd = new BasicParamEvent(); 
        $this->onFilterSuccess = new BasicParamEvent();
        $this->onFilterFail = new BasicParamEvent();     
        $this->onLog= new BasicParamEvent();
    }
    abstract public function parse($path);
    abstract public function getPath();

    public function getSettings() : int
    {
        return $this->settings;
    }

    public function getOptions() : array
    {
        return $this->findOptions;
    }
}

class HtmlParser extends BaseHtmlParser
{
    private $startUrl = '';
    private $startDomain = '';

    private array $urlVisitedStack = [];
    private array $urlToParseStack = [];

    function __construct(IHtmlCrawler $crawler, array $findOptions, int $settings)
    {
        parent::__construct($findOptions, $settings);
        $this->crawler = $crawler;
    }
    private function log($val)
    {
        $this->onLog->invoke($val);
    }
    public function getPath()
    {
        return $this->url;
    }
    public function parse($url)
    {
        $this->startUrl = $url;
        $this->startDomain = parse_url($this->startUrl , PHP_URL_HOST);
        array_push($this->urlToParseStack, $url);

        $this->log("--------START--------");
        $this->onStart->invoke($url);    

        $this->parseJob();

        $this->onEnd->invoke($url);    
        $this->log("-------END--------");
    }

    private function parseJob()
    {
        while (empty($this->urlToParseStack) === false)
        {
            $url = array_pop($this->urlToParseStack);

            $this->log("Creating DOM and loading HTML file from \"$this->startUrl\": ");
            $this->onFileLoading->invoke($url);

            $this->crawler->loadHTMLFile($url);
            $this->onFileLoaded->invoke($url);

            $this->crawlPage();
            array_push($this->urlVisitedStack, $this->startUrl);
        }
    }

    private function crawlPage()
    {
        foreach($this->findOptions as $tagOptions)
        {
            $elements = $this->crawler->getElementsByTagName($tagOptions->getValue());
            $this->log(count($elements) . " elements found");

            foreach($elements as $node)
            {
                $this->onFind->invoke($node->nodeValue);

                $this->parseElement($node, $tagOptions, $tagOptions->getFilter());
            }
        }       
    }

    private function parseElement($node, $tagOptions, $tagFilter)
    {
        foreach($tagOptions->getOptions() as $attrOptions)
        {
            $this->parseAttribute($node, $attrOptions);
        }
        return $this->doFilter($tagFilter, $node->nodeValue);
    }

    private function parseAttribute($nodeValue, $attrOptions)
    {
        foreach ($attrOptions as $option)
        {
            $attrName = $option->getValue();
            $attrValue = $nodeValue->getAttribute($attrName);

            $newUrl = $this->doFilter($option->getFilter(), $attrValue);

            if ($attrName == 'href' && $this->canAddNewUrl($newUrl))
            {
                array_push($this->urlToParseStack, $newUrl);
            }
        }
    }

    private function canAddNewUrl($newUrl)
    {
        if ($this->isSettingsSet(ParserSettings::Recursive) && $this->canVisit($newUrl) === true)
        {
            return false;
        }
        return true;
    }

    private function isSettingsSet(int $flags)
    {
        return $this->settings & $flags;
    }

    private function isAlreadyVisited($url)
    {               
        return $url == $this->startUrl || array_search($url, $this->urlVisitedStack);      
    }
    private function canVisit($url)
    {
        $hrefDomain = parse_url($url , PHP_URL_HOST);
        $isCurrentDomain = $hrefDomain == $this->startDomain;
        
        return 
        $this->isAlreadyVisited($url) === false && 
        ($isCurrentDomain ? true :
            $this->isSettingsSet(ParserSettings::GoExternal));
    }

    private function doFilter($filter, $val)
    {
        if (empty($filter))
        {
            return;
        }
        $this->log("Filtering \"$val\"");
        $filterValue = $filter->run($val);

        if ($filterValue !== false)
        {
            $this->log("Success");  
            $this->onFilterSuccess->invoke($filterValue);
            return $filterValue;
        }
        else
        {
            $this->log("Fail");  
            $this->onFilterFail->invoke($filterValue);
        }
        return $val;
    }
}

?>