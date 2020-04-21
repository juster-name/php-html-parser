<?php
require_once ('filter.php');
namespace Test\Parser;

interface IAttribute
{
    public function getName();
    public function getFilter();
}

class Attribute implements IAttribute
{
    public $name;
    /** @var IFilter */
    public $filter;

    function __construct($name, IFilter $filter = null)
    {
        $this->name = $name;
        $this->filter = $filter;
    }

    public function getName()
    {
        return $this->name;
    }
    public function getFilter()
    {
        return $this->filter;
    }
}

?>