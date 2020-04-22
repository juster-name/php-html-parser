<?php
namespace Test\Parser;
require_once ('action.php');

interface IOption
{
    public function getValue();
    public function getOptions() : array;
    public function getFilter() : IActionParam;
}

class Option implements IOption
{
    public $value;
    public IActionParam $filter;
    public array $options;

    function __construct($val, $filter = null, array ... $options)
    {
        $this->value = $val;
        $filter == null ?  $this->filter = new EmptyAction() : $this->filter = $filter;
        $this->options = $options;
    }
    public function getOptions() : array
    {
        return $this->options;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function getFilter() : IActionParam
    {
        return $this->filter;
    }
}

/*
interface IHtmlOption
{
    public function getTagOption() : IOption;
    public function getAttrOption() : IOption;    
}

abstract class BaseOption implements IOption
{
    public $val;
    public IActionParam $filter;

    function __construct($val, IActionParam $filter = null)
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
class SimpleOption extends BaseOption
{
    function __construct(string $name, IActionParam $filter = null)
    {
        parent::__construct($name, $filter);
    }
}

class AttributeOption extends BaseOption
{
    function __construct(IAttribute $attr, IActionParam $filter = null)
    {
        parent::__construct($attr, $filter);
    }
}

class TagOption extends BaseOption
{
    function __construct(ITag $attr, IActionParam $filter = null)
    {
        parent::__construct($attr, $filter);
    }
}

class HtmlOption implements IHtmlOption
{
    public $attrOpt;
    public $tagOpt;

    function __construct(IOption $attrOpt, IOption $tagOpt)
    {
        $this->attrOpt = $attrOpt;
        $this->tagOpt = $tagOpt;
    }

    public function getTagOption() : IOption
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

    function __construct(HtmlOption ... $htmlOpts)
    {
        $this->htmlOpts = $htmlOpts;
    }

    public function toArray() : array {
        return $this->values;
      }

    public function getIterator() {
        return new \ArrayIterator($this->htmlOpts);
      }
}
*/
?>