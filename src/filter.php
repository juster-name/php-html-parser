<?php
namespace Test\Parser;

interface IFilter
{
    public function filter($var);
}

class HrefFilter implements IFilter
{
    public $domainName;

    function __construct($domainName)
    {
        $this->domainName = $domainName;
    }

    public function filter($var)
    {
        if (filter_var($var, FILTER_VALIDATE_URL) === false) 
        {
            return $$this->domainName . "/" . $var;
        }
        return $var;
    }
}

class ImgFilter implements IFilter
{
    public function filter($var)
    {
    }
}

?>