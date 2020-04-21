<?php
namespace Test\Parser;

interface IActionParam
{
    public function run($param);
}

class HrefFindAction implements IActionParam
{
    public function run($param)
    {
        // todo
    }
}

class TestFindAction implements IActionParam
{
    public function run($param)
    {
        echo "TestFindAction run( $param )";
    }
}

?>