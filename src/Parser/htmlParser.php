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
    protected $startUrl = '';
    protected IOption $baseOption;

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
    public BasicParamEvent $onNewUrlFound;
    public BasicParamEvent $onNewUrlAdded;

    function __construct(array $findOptions, int $settings, ICrawler $crawler, IActionParam $hrefFilter)
    {
        $this->findOptions = $findOptions;
        $this->hrefFilter = $hrefFilter;
        $this->settings = $settings;
        $this->crawler = $crawler;
        $this->baseOption = new Option('a', null, new Option('href', null));

        $this->onFind = new BasicParamEvent();
        $this->onError = new BasicParamEvent();
        $this->onFileLoading = new BasicParamEvent();
        $this->onFileLoaded = new BasicParamEvent();
        $this->onStart = new BasicParamEvent(); 
        $this->onEnd = new BasicParamEvent(); 
        $this->onNewUrlFound = new BasicParamEvent();
        $this->onNewUrlAdded = new BasicParamEvent();
        $this->onFilterSuccess = new BasicParamEvent();
        $this->onFilterFail = new BasicParamEvent();     
        $this->onLog= new BasicParamEvent();
    }

    public abstract function parse($url);

    public function prepare($url)
    {
        $this->startUrl = $this->hrefFilter->run($url);
    }
    public function getPath()
    {
        return $this->startUrl;
    }

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

    public function addOptions(IOption $options)
    {
        array_push($this->findOptions, $options);
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
class ParserMessage
{
    public $value;
    public IOption $option;

    function __construct($value, $option)
    {
        $this->value = $value;
        $this->option = $option;
    }
}

class HtmlParser extends BaseHtmlParser
{
    private $startDomain = '';
    private string  $curUrl = '';
    private array $urlVisitedStack = [];
    private array $urlToParseStack = [];
    private const MAX_PARSE_STACK_SIZE = 3;

    function __construct(array $findOptions, int $settings)
    {
        $htmlCrawler =  new HtmlCrawler();
        $hrefFilter = new HrefFilter();

        parent::__construct($findOptions, $settings, $htmlCrawler, $hrefFilter);
    }
    public function getPath()
    {
        return $this->startUrl;
    }

    private function log($val)
    {
        //$this->onLog->invoke($val);
    }

    public function parse($url)
    {       
        parent::prepare($url);

        $this->startDomain = parse_url($this->startUrl , PHP_URL_HOST);
        $this->urlToParseStack[$this->startUrl] = '';

        $this->log("--------START--------");
        $this->onStart->invoke($this->startUrl);   

        $this->parseJob(HtmlParser::MAX_PARSE_STACK_SIZE);

        $this->onEnd->invoke($this->startUrl);    
        $this->log("-------END--------");
    }

    private function parseJob($urlCount)
    {
        while (empty($this->urlToParseStack) === false)
        {
            $this->curUrl = array_key_last($this->urlToParseStack);
            array_pop($this->urlToParseStack);
            
            $this->log("Creating DOM and loading HTML file from \"$this->curUrl\": ");
            $this->onFileLoading->invoke($this->curUrl);

            try
            {
                $this->crawler->load($this->curUrl);
            }
            catch(\Exception $ex)
            {
                echo $ex;
            }
            $this->onFileLoaded->invoke($this->curUrl);
            
            $this->crawlPage($this->findOptions);
            $this->urlVisitedStack[$this->curUrl] = '';  
            $this->crawlPage([$this->baseOption], $urlCount);

       
        }
    }

    private function crawlPage(array $mainOptions, int $maxCount = -1)
    {
        foreach($mainOptions as $option)
        {
            $optValue = $option->getValue();
            $tagElements = $this->crawler->crawl($optValue);
            $this->log(count($tagElements) . " '$optValue' found");

            foreach($tagElements as $node)
            {
                $this->onFind->invoke($node->nodeValue);
                $this->parseElement($node, $option);
            }
        }
    }

    private function parseElement($node, IOption $tagOption)
    {
        foreach($tagOption->getOptions() as $attrOption)
        {
            $this->parseAttribute($node, $attrOption);
        }
        return $this->doFilter($node->nodeValue, $attrOption);
    }

    private function parseAttribute($nodeValue, IOption $attrOption)
    {
        $attrName = $attrOption->getValue();
        $attrValue = $nodeValue->getAttribute($attrName);

        if ($attrName == 'href')
        {             
            $newUrl = $this->hrefFilter->run($attrValue);

            $this->onNewUrlFound->invoke($newUrl);

            if ($this->canAddNewUrl($newUrl))
            {
                $this->urlToParseStack[$newUrl] = '';
                $this->onNewUrlAdded->invoke($newUrl);
            }
        }
   
        $this->doFilter($attrValue, $attrOption);    
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
        return $url == $this->startUrl || $url == $this->curUrl ||
            array_key_exists ($url, $this->urlVisitedStack) ||
            array_key_exists ($url, $this->urlToParseStack);      
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

    private function doFilter($val, IOption $option)
    {
        $this->log("Filtering \"$val\"");
        $filterValue = $option->getFilter()->run($val);

        if (isset($filterValue) && $filterValue !== false)
        {
            $callbackMsg = new ParserMessage($filterValue, $option);

            $this->log("Success");  
            $this->onFilterSuccess->invoke($callbackMsg);
            return $filterValue;
        }
        else
        {
            $callbackMsg = new ParserMessage($val, $option);

            $this->log("Fail");  
            $this->onFilterFail->invoke($callbackMsg);
        }
        return $val;
    }
}

?>