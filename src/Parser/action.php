<?php
namespace Test\Parser;

use Exception;

interface IActionParam
{
    public function run($param);
    public function getName() : string;
}
class InvokeActionParam implements IActionParam
{
    public $callBackfunc;

    function __construct($callBackfunc)
    {
        if (is_callable($callBackfunc) == false)
        {
            throw new Exception("Function \"$callBackfunc\" is not callable");
        }
        $this->callBackfunc = $callBackfunc;
    }

    public function run($param)
    {       
        $this->callBackfunc->__invoke($param);
    }
    public function getName() : string
    {
       return spl_object_hash($this);
    }
}

class UserCallActionParam implements IActionParam
{
    public string $callbackName;

    function __construct(string $callbackName)
    {
        $this->callbackName = $callbackName;
    }
    public function run($param)
    {
        $b1 = empty($param);
        $b2 = empty($this->getName());

        if (empty($param) || empty($this->getName()))
        {
            throw new Exception("Unable to run action \"$this->callbackName\" with param \"$param\"");            
        }
        
        call_user_func("$this->callbackName", $param);
    }
    public function getName() : string
    {
       return $this->callbackName;
    }
}

class EmptyAction implements IActionParam
{
    public function run($param)
    {
    }
    public function getName() : string
    {
        return get_class($this);
    }
}

class HrefFindAction implements IActionParam
{
    public  function run($param)
    {
        // todo
    }
    public function getName() : string
    {
        return get_called_class($this);
    }
}

class TestFindAction implements IActionParam
{
    public  function run($param)
    {
        echo "TestFindAction run( $param )";
    }
    public function getName() : string
    {
        return get_class($this);
    }
}

?>