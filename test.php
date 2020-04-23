<?php
namespace Test\Parser;
require_once (__DIR__."/vendor/autoload.php");
set_include_path(__DIR__."\\src\\Parser");
require ('htmlParser.php');
require ('filters.php');

$internal = [];
$external = [];

$url = "https://www.uavi.top/login.php/";



$hrefFilter =  new HrefFilter($url);
$htmlCrawler = new HtmlCrawler();

$aHrefOption = new Option('a', null, 
    [new Option('href', $hrefFilter)]);

use Test\Parser\ParserSettings as PS;

$p = new HtmlParser($htmlCrawler, [$aHrefOption], PS::Recursive);
$logAction = new UserCallActionParam("Test\\Parser\\_log");

$saveInternalAction = new InvokeActionParam(function($url) use (&$internal, &$hrefFilter)
{ 
    if ($hrefFilter->getUrlHost() == parse_url($url, PHP_URL_HOST))
    {
        array_push($internal, $url);
    }
});

$saveExternalAction = new InvokeActionParam(function($url) use (&$external, &$hrefFilter)
{     
    if ($hrefFilter->getUrlHost() != parse_url($url, PHP_URL_HOST))
    {
        array_push($external, $url);
    }
});

$p->onLog->add($logAction);

$p->onFilterSuccess->add($saveInternalAction);
$p->onFilterSuccess->add($saveExternalAction);

$p->parse($url);

echo "\n\n INTERNALS: ";
foreach ($internal as $url)
{
    echo "\n$url";
}

echo "\n\n EXTERNALS: ";
foreach ($external as $url)
{
    echo "\n$url";
}
/*
$recursiveAction = new InvokeActionParam(function ($url) use ($p)
{
    $domain = urlToDomain($p->getPath());

    if (strpos($url, $domain) !== false) 
    {
        $p->parse($url);
    }
});

$p->onFilterSuccess->add($recursiveAction);
$p->parse($url);

function urlToDomain($url) {
    return implode(array_slice(explode('/', preg_replace('/https?:\/\/(www\.)?/', '', $url)), 0, 1));
 }

 */
//$p->onStart->add(new UserCallActionParam("\\lol"));
//$p->onStart->add(new TestFindAction());
//$p->parse("https://google.com");

/*
$domain = "https://translate.google.com.ua/";
$domainShort = "translate.google.com.ua";

$queue = [];
$img = [];
$internal = [];
$external = [];
$error = [];
array_push($queue, $domain);
array_push($internal, $domain);
*/

function _logInFile($val)
{
    $curDir = dirname(__FILE__);

    $fname = "log.txt";
    

    $fullVal = date("H:i:s") . ": " . $val . "\r\n"; 

    file_put_contents($curDir . "/$fname", $fullVal, FILE_APPEND);
}

function _log($val)
{
    $fname = "log.txt";
    _logInFile($val, $fname);

    $fullVal = date("H:i:s") . ": " . $val . "\r\n"; 
    echo $fullVal; 
}
/*
function parse($tagsAtts)
{
    global $document;
    global $domain;
    global $domainShort;
    global $internal;
    global $external;

    foreach ($tagsAtts as $tagName => $att)
    {
        _log("Looping through <$tagName>");

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
}
function parseAtt($tagName, $attName, $findFunc)
{
    $att = $tagName->getAttribute($attName);

    if (empty($att))
    {
        return false;
    }

    call_user_func($findFunc, $att);
}

function findHref($href)
{
    global $domain;
    global $domainShort;
    global $internal;
    global $external;
    
    if (filter_var($href, FILTER_VALIDATE_URL) === false) 
    {
        $href = $domain . "/" . $href;
    }

    _log("\t".$href);

    if (array_search($href, $internal) || array_search($href, $external)) 
    {
        _log("\t\tAlready saved, continue");
        continue;
    }

    if (strpos($href, $domainShort) !== false) 
    {
        _log("\t\tSaving in internal");
        array_push($queue, $href);
        array_push($internal, $href);
    } 
    else 
    {
        _log("\t\tSaving in external");
        array_push($external, $href);
    }
}

_log("--------START--------\r\n");

try
{
    while (empty($queue) === false) 
    {
        $url = array_pop($queue);

        try
        {
            _log("\r\n\r\nCreating DOM and loading HTML file: ");
            $document = new DOMDocument();
            $loaded = @$document->loadHTMLFile($url);
            _log("OK.\r\n");
        }
        catch (Exception $e)
        {
            _log("ERROR (Logged)\r\n");
            array_push($error, "DOM load html exception. Url: $url\r\n$e");
            continue;
        }
        parse(
            ['a'=> 
                ['href', 'findHref'],
            ]);

        
    }
}
catch(Exception $e)
{
    array_push($error, "Queue loop:" . $e);
    _log("Unexpected ERROR occurred (logged)");
}

_log("\r\n--------END--------");


_logInFile("-------START--------","internal.txt");
foreach ($internal as $element) 
{
    _logInFile($element, "internal.txt");
}
_logInFile("-------END--------","internal.txt");

_logInFile("-------START--------","external.txt");
foreach ($external as $element) 
{
    _logInFile($element, "external.txt");
}
_logInFile("-------END--------","external.txt");

_logInFile("-------START--------","error.txt");
foreach ($error as $element)
{
    _logInFile($element, "error.txt");
}
_logInFile("-------END--------","error.txt");
*/
?>