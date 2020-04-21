<?php

require_once ('attribute.php');
require_once ('htmlLoader.php');

namespace Test\Parser;

interface ITag
{
    public function getName();
    public function getAttribute($name);
    public function getAttributes();
}

class Tag implements ITag
{
    public $name;

    function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAttribute($name)
    {

    }
}

?>