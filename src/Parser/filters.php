<?php
namespace Test\Parser;

class HrefFilter implements IActionParam
{
    public $domainName;

    function __construct($domainName)
    {
        $this->domainName = $domainName;
    }

    public function run($var)
    {
        if (filter_var($var, FILTER_VALIDATE_URL) === false) 
        {
            return $this->domainName . "/" . $var;
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