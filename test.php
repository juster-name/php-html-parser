<?php
namespace Test\Parser;
require_once (__DIR__."/vendor/autoload.php");
set_include_path(__DIR__."\\src\\Parser");
require ('htmlParser.php');
require ('filters.php');

$internal = [];
$external = [];
$imgs = [];

$url = "https://google.com.ua/";

use Test\Parser\ParserSettings as PS;
$p = new HtmlParser([], PS::Recursive);

//$hrefFilter =  new HrefFilter($url);
$saveImgAction= new InvokeActionParam(function($msg) use (&$imgs, &$internal, &$p)
{     
    //$fullUrl = $p->hrefFilter->run($urlArg);
    $value = $msg->value;
    
    $tagName = $msg->option->getParent()->getValue() ?: '';
    $attrName = $msg->option->getValue();

    if ($tagName == 'img' && $attrName == 'src')
    {
        if (key_exists($value, $imgs))
        {
            return;
        }
        _logInFile($value, 'imgs.txt');
        $imgs[$value] = '';
    }

});


$imgOpts = new Option('img', null, new Option('src', new ImgFilter($p)));

$p->addOptions($imgOpts);

$logAction = new UserCallActionParam("Test\\Parser\\_log");

$saveInternalAction = new InvokeActionParam(function($urlArg) use (&$internal, &$p)
{ 
    $initPath = $p->getPath();

    if (parse_url($initPath, PHP_URL_HOST) == parse_url($urlArg, PHP_URL_HOST))
    {
        if (key_exists($urlArg, $internal))
        {
            return;
        }
        _log($urlArg);
        _logInFile($urlArg, 'internals.txt');
        $internal[$urlArg] = '';
    }
});

$saveExternalAction = new InvokeActionParam(function($urlArg) use (&$external, &$p)
{     
    if (parse_url($p->getPath(), PHP_URL_HOST) != parse_url($urlArg, PHP_URL_HOST))
    {
        if (key_exists($urlArg, $external))
        {
            return;
        }
        _log($urlArg);
        _logInFile($urlArg, 'externals.txt');
        $external[$urlArg] = '';
    }
});

//$p->onStart->add($logAction);
//$p->onEnd->add($logAction);
$p->onFilterSuccess->add($saveImgAction);
$p->onNewUrlFound->add($saveInternalAction);
$p->onNewUrlFound->add($saveExternalAction);

$p->parse($url);

echo "\n\n INTERNALS: ";
foreach ($internal as $url => $val)
{
    echo "\n$url";
}

echo "\n\n EXTERNALS: ";
foreach ($external as  $url => $val)
{
    echo "\n$url";
}

echo "\n\n IMAGES: ";
foreach ($imgs as  $url => $val)
{
    echo "\n$url";
}


function _logInFile($val, $path = 'log.txt')
{
    $curDir = dirname(__FILE__);

    $fname = $path;
    
    //$fullVal = date("H:i:s") . ": " . $val . "\r\n"; 

    file_put_contents($curDir . "/$fname", $val . "\r\n", FILE_APPEND);
}

function _log($val)
{
    $fname = "log.txt";
    //_logInFile($val, $fname);

    $fullVal = date("H:i:s") . ": " . $val . "\r\n"; 
    echo $fullVal; 
}
?>