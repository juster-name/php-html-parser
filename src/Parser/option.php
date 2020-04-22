<?php
namespace Test\Parser;
require_once ('action.php');
require_once ('attribute.php');

interface IOption
{
    public function getValue();
    public function getFilter() : IActionParam;
}
interface IHtmlOption
{
    public function getTagrOption() : IOption;
    public function getAttrOption() : IOption;    
}

abstract class Option implements IOption
{
    public $val;
    public IActionParam $filter;

    function __construct($val, IActionParam $filter)
    {
        $this->attr = $val;
        $this->filter = $filter;
    }
    public function getValue()
    {
        return $this->val;
    }
    public function getFilter() : IActionParam
    {
        return $this->filter;
    }
}

class AttributeOption extends Option
{
    function __construct(IAttribute $attr, IActionParam $filter)
    {
        parent::__construct($attr, $filter);
    }
}

class TagOption extends Option
{
    function __construct(ITag $attr, IActionParam $filter)
    {
        parent::__construct($attr, $filter);
    }
}
class HtmlOption implements IHtmlOption
{
    public $attrOpt;
    public $tagOpt;

    function __construct(AttributeOption $attrOpt, TagOption $tagOpt)
    {
        $this->attrOpt = $attrOpt;
        $this->tagOpt = $tagOpt;
    }

    public function getTagrOption() : IOption
    {
        return $this->tagOpt ;
    }
    public function getAttrOption() : IOption
    {
        return $this->attrOpt;
    }
}

class HtmlOptionsArray implements \IteratorAggregate
{
    private $htmlOpts = [];

    public function toArray() : array {
        return $this->values;
      }

    public function getIterator() {
        return new \ArrayIterator($this->htmlOpts);
      }
}

?>