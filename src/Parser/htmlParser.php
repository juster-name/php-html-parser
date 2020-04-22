<?php
namespace Test\Parser;

use DOMNodeList;

require_once ("event.php");
require_once ("crawler.php");

interface IParser
{
    public function parse($path);   
    public function getOptions() : array;
}

abstract class BaseHtmlParser implements IParser
{
    public array $findOptions;
    public BasicParamEvent $onLog;
    public BasicParamEvent $onFind;
    public BasicParamEvent $onFilterSuccess;
    public BasicParamEvent $onFilterFail;
    public BasicParamEvent $onError;
    public BasicParamEvent $onFileLoading;
    public BasicParamEvent $onFileLoaded;
    public BasicParamEvent $onStart;
    public BasicParamEvent $onEnd;
    
    function __construct(array $options)
    {
        $this->findOptions = $options;
    
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
    public function getOptions() : array
    {
        return $this->findOptions;
    }
}

class HtmlParser extends BaseHtmlParser
{
    function __construct(IHtmlCrawler $crawler, array $findOptions)
    {
        parent::__construct($findOptions);
        $this->crawler = $crawler;
    }
    private function log($val)
    {
        $this->onLog->invoke($val);
    }
    public function parse($url)
    {
        $this->log("--------START--------\r\n");
        $this->log("\r\n\r\nCreating DOM and loading HTML file: ");

        $this->crawler->loadHTMLFile($url);
        $this->onStart->invoke($url);
        
        $this->parsePage();
        //while (empty($queue) === false) 
        //{
        //    $url = array_pop($queue);
        //    $this->parsePage();         
        //}
        $this->onEnd->invoke($url);
        $this->log("\r\n--------END--------");
    }

    private function parsePage()
    {
        foreach($this->findOptions as $tagOptions)
        {
            $elements = $this->crawler->getElementsByTagName($tagOptions->getValue());
            $this->log("\n".count($elements) . " elements found");

            foreach($elements as $node)
            {
                $this->onFind->invoke($node->nodeValue);

                $this->parseElement($node, $tagOptions, $tagOptions->getFilter());
            }
            /*
            try
            {
              $allATags = $document->getElementsByTagName("a");
            }
            catch(Exception $e)
            {
              array_push($error, "ERROR in getting <a> tag: " . $e);
              _log("ERROR in getting <a> tag (logged)");
            }
            */
        }        
    }

    private function parseElement($node, $tagOptions, $tagFilter)
    {
        foreach($tagOptions->getOptions() as $attrOptions)
        {
            $this->parseAttribute($node, $attrOptions);
        }
        $this->doFilter($tagFilter, $node->nodeValue);
    }

    private function parseAttribute($nodeValue, $attrOptions)
    {
        foreach ($attrOptions as $option)
        {
            $attrValue = $nodeValue->getAttribute($option->getValue());
            $this->doFilter($option->getFilter(), $attrValue);
        }
    }

    private function doFilter($filter, $val)
    {
        if (empty($filter))
        {
            return;
        }
        $this->log("\n\tFiltering".$val);
        $state = $filter->run($val);

        if ($state !== false)
        {
            $this->log("\n\t\tSuccess");  
            $this->onFilterSuccess->invoke($val);
        }
        else
        {
            $this->log("\n\t\Fail");  
            $this->onFilterFail->invoke($val);
        }
    }
}

?>