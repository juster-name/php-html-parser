<?php
namespace Test\Parser;
require_once ('action.php');

interface IOption
{
    public function getValue();
    public function getOptions() : array;
    public function getFilter() : IActionParam;

    public function setValue($value);
    public function setOptions(array $options);
    public function setFilter(IActionParam $filter);

    public function getParent();
}

class Option implements IOption
{
    public $value;
    public IActionParam $filter;
    public array $options;
    private $parentOption;

    function __construct($val, $filter = null, IOption ... $options)
    {
        $this->parentOption = null;
        $this->value = $val;
        $filter == null ?  $this->filter = new EmptyAction() : $this->filter = $filter;
        $this->options = $options ?: [];

        foreach($this->options as $opt)
        {
            $opt->parentOption = $this;
        }
    }
    public function getParent()
    {
        return $this->parentOption;
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

    public function setValue($value)
    {
        $this->value = $value;
    }
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
    public function setFilter(IActionParam $filter)
    {
        $this->filter = $filter;
    }
}
?>