<?php
namespace Test\Parser;
require_once ('action.php');

class HrefFilter implements IActionParam
{
    private $urlHost;
    private $urlScheme;
    private $url;

    function __construct($url)
    {
        $this->setUrl($url);
    }

    public function setUrl($url) 
    {
        $this->url = $url;
        $this->urlHost = parse_url($this->url , PHP_URL_HOST);
        $this->urlScheme = parse_url($this->url , PHP_URL_SCHEME);
    }

    public function getUrlScheme() 
    {
        return $this->urlScheme;
    }

    public function getUrlHost() 
    {
        return $this->urlHost;
    }

    public function getUrl() 
    {
        return $this->url;    
    }
      
    public function run($var)
    {
        if (filter_var($var, FILTER_VALIDATE_URL) === false) 
        {
            return $this->urlScheme . "://" . $this->urlHost . "/" . $var;
        }
        return $var;
    }
    public function getName() : string
    {
        return get_class($this);
    }
}

class ImgFilter implements IActionParam
{
    public function run($var)
    {

    }
    public function getName() : string
    {
        return get_class($this);
    }
}

?>