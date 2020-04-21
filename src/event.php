<?php
require_once ("action.php");
namespace Test\Parser;

interface IBasicParamEvent
{
    public function add(IActionParam $callback);
    public function remove(IActionParam $callback);
    public function invoke($param);
}

class BasicEvent implements IBasicParamEvent
{
    public $actions = [];
    public function add(IActionParam $callback)
    {
        array_push($callback);
    }
    public function remove(IActionParam $callback)
    {
        $index = array_search($callback->getName(), $this->actions);
        if ($index === false)
        {
            return;
        }
        array_splice($this->actions, $index, 1);
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