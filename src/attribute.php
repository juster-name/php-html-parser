<?php
namespace Test\Parser;

require_once ('filter.php');

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