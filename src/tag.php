<?php

require_once ('attribute.php');
require_once ('htmlLoader.php');

namespace Test\Parser;

interface ITag
{
    public function getName();
    public function getAttribute($name) : IAttribute;
}

class Tag implements ITag
{
    /** @var \DOMElement */
    private $domElement;

    function __construct(\DOMElement $element)
    {
        $this->domElement = $element;
    }

    public function getName()
    {
        return $this->domElement->tagName;
    }

    public function getAttribute($name)
    {
        return $this->domElement->getAttribute($name);
    }
}

?>