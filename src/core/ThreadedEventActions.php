<?php


namespace event\core;


use Thread;

class ThreadedEventActions extends Thread
{
    private $obj;
    private $action;

    /**
     * ThreadedEventActions constructor.
     * @param $obj
     * @param $action
     */
    public function setParams($obj, $action)
    {
        $this->obj = $obj;
        $this->action = $action;
    }


    /**
     *
     */
    public function run()
    {
        $obj = $this->obj;
        $method = $this->action;
        $obj->$method();
    }
}