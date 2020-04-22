<?php
namespace Test\Parser;

interface IActionParam
{
    public  function run($param);
}
class UserCallActionParam implements IActionParam
{
    public $callbackName;

    function __construct($callbackName)
    {
        $this->callbackName = $callbackName;
    }
    public function run($param)
    {
        call_user_func("$this->callbackName", $param);
    }
}

class HrefFindAction implements IActionParam
{
    public  function run($param)
    {
        // todo
    }
}

class TestFindAction implements IActionParam
{
    public  function run($param)
    {
        echo "TestFindAction run( $param )";
    }
}

?>