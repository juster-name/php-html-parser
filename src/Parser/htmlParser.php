<?php
namespace Test\Parser;

require_once ("event.php");
require_once ("crawler.php");

interface IParser
{
    public function parse(string $path);

    public function getPath();
}

abstract class ParserSettings
{
    public const Recursive = 0b0001;
    public const GoExternal = 0b0010;
}

abstract class BaseHtmlParser implements IParser
{
    public ICrawler $crawler;
    public array $findOptions;
    public IActionParam $hrefFilter;

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
    
    function __construct(array $findOptions, int $settings, ICrawler $crawler, IActionParam $hrefFilter)
    {
        $this->findOptions = $findOptions;
        $this->hrefFilter = $hrefFilter;
        $this->settings = $settings;
        $this->crawler = $crawler;

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

    public function getCrawler()
    {
        return $this->crawler;
    }
    public function setCrawler(ICrawler $crawler)
    {
        $this->crawler = $crawler;
    }
    public function getSettings() : int
    {
        return $this->settings;
    }

    public function getOptions() : array
    {
        return $this->findOptions;
    }

    public function setOptions(array $options)
    {
        $this->findOptions = $options;
    }
    public function setSettings(int $settings)
    {
        $this->settings = $settings;
    }

    public function setHrefFilter(IActionParam $hrefFilter)
    {
        $this->hrefFilter = $hrefFilter;
    }
    public function getHrefFilter()
    {
        return $this->hrefFilter;
    }
}

class HtmlParser extends BaseHtmlParser
{
    private $startUrl = '';
    private $startDomain = '';

    private array $urlVisitedStack = [];
    private array $urlToParseStack = [];

    function __construct(array $findOptions, int $settings)
    {
        $htmlCrawler =  new HtmlCrawler();
        $hrefFilter = new HrefFilter();
        parent::__construct($findOptions, $settings, $htmlCrawler, $hrefFilter);
    }
    private function log($val)
    {
        $this->onLog->invoke($val);
    }
    public function getPath()
    {
        return $this->startUrl;
    }

    public function parse($url)
    {       
        $this->startUrl = $this->hrefFilter->run($url);;
        $this->startDomain = parse_url($this->startUrl , PHP_URL_HOST);
        array_push($this->urlToParseStack, $this->startUrl);

        $this->log("--------START--------");
        $this->onStart->invoke($this->startUrl);    

        $this->parseJob();

        $this->onEnd->invoke($this->startUrl);    
        $this->log("-------END--------");
    }

    private function parseJob()
    {
        while (empty($this->urlToParseStack) === false)
        {
            $url = array_pop($this->urlToParseStack);

            $this->log("Creating DOM and loading HTML file from \"$this->startUrl\": ");
            $this->onFileLoading->invoke($url);

            try
            {
                $this->crawler->load($url);
            }
            catch(\Exception $ex)
            {
                echo $ex;
            }
            $this->onFileLoaded->invoke($url);

            $this->crawlPage();
            array_push($this->urlVisitedStack, $this->startUrl);
        }
    }

    private function crawlPage()
    {
        foreach($this->findOptions as $tagOptions)
        {
            $elements = $this->crawler->crawl($tagOptions->getValue());
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

            if ($attrName == 'href')
            {
                $newUrl = $this->hrefFilter->run($newUrl);
                if ($this->canAddNewUrl($newUrl))
                {
                    array_push($this->urlToParseStack, $newUrl);
                }
            }
        }
    }

    private function canAddNewUrl($newUrl)
    {
        if ($this->isSettingsSet(ParserSettings::Recursive))
        {
            $canVisit = $this->canVisit($newUrl);
            return $canVisit;
        }
        return false;
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

        if (isset($filterValue) && $filterValue !== false)
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