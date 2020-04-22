<?php

require_once (__DIR__."/vendor/autoload.php");

//echo __DIR__ . "\\vendor\\autoload.php";
/*

echo __DIR__ . "\\vendor\\autoload.php";
//require ('src/parser.php');
*/
use Test\Parser as p;


function lol($param)
{
    echo "\n". $param . "\n";
}

$p = new p\HtmlParser(new p\HtmlCrawler());

$p->onStart->add(new p\UserCallActionParam("\\lol"));
$p->onStart->add(new p\TestFindAction());
$p->parse("https://google.com");

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

function _logInFile($val, $fname = null)
{
    $curDir = dirname(__FILE__);

    if (empty($fname))
    {
        $fname = "log.txt";
    }

    $fullVal = date("H:i:s") . ": " . $val . "\r\n"; 

    file_put_contents($curDir . "/$fname", $fullVal, FILE_APPEND);
}

function _log($val, $fname = null)
{
    $fname ?: "log.txt";
    _logInFile($val, $fname);

    $fullVal = date("H:i:s") . ": " . $val . "\r\n"; 
    echo $fullVal; 
}

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