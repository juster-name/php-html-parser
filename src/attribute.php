<?php
require_once ('filter.php');
namespace Test\Parser;

interface IAttribute
{
    public function getName();
}

class Attribute implements IAttribute
{
    /** @var \DOMAttr */
    private $domAttr;

    function __construct(\DOMAttr $domAttr)
    {
        $this->domAttr = $domAttr;
    }

    public function getName()
    {
        return $this->domAttr->name;
    }
}

?>