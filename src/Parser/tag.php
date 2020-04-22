<?php
namespace Test\Parser;

require_once ('attribute.php');
require_once ('htmlLoader.php');


interface ITag
{
    public function getName();
    public function getAttribute($name) : IAttribute;
}

class Tag implements ITag
{
    function __construct(\DOMElement $element)
    {
        $this = $element;
    }

    public function getName()
    {
        return $this->tagName;
    }

    public function getAttribute($name)
    {
        return $this->getAttribute($name);
    }
}

?>