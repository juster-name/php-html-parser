<?php
namespace Test\Parser;

interface IActionParam
{
    public function getName();
    public function run($param);
}

class HrefFindAction implements IActionParam
{
    public function getName()
    {
        return get_class($this);
    }
    public function run($param)
    {
        // todo
    }
}

?>