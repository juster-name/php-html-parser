<?php
namespace Test\Parser;

require_once ("action.php");

interface IBasicParamEvent
{
    public function add(IActionParam $callback);
    public function remove(IActionParam $callback);
    public function invoke($param);
}

class BasicParamEvent implements IBasicParamEvent
{
    public $actions = [];
    public function add(IActionParam $callback)
    {
        $funcName = $callback->getName();

        $this->actions[$funcName] = $callback;
    }
    public function remove(IActionParam $callback)
    {
        $funcName = $callback->getName();

        unset($this->actions[$funcName]);
    }
    public function invoke($param)
    {
        foreach($this->actions as $action)
        {
            $action->run($param);
        }
    }
}
?>