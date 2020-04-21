<?php

require_once ('attribute.php');
require_once ('htmlLoader.php');

namespace Test\Parser;

interface ITag
{
    public function getName();
    public function getAttribute($name) : IAttribute;
}
/*
interface ITagArray extends \Traversable
{
    
}

class TagArray implements \IteratorAggregate
{
    private $items = [];

    public function __construct($items = [])
    {
        $this->items = $items;
    }

    public function getIterator()
    {
        return (function () {
            foreach($this->items as $key=>$val)
            {
                yield $key => $val;
            }
        })();
    }
}
*/

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