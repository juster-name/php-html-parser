<?php
namespace Test\Parser;

require_once ('filter.php');

interface IAttribute
{
    public function getName();
}

class Attribute extends \DOMAttr implements IAttribute
{
    function __construct(\DOMAttr $domAttr)
    {
        $this = $domAttr;
    }

    public function getName()
    {
        return $this->domAttr->name;
    }
}

?>