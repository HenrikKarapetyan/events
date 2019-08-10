<?php


namespace event\core;


/**
 * Class EventEmitter
 * @package event
 */
class EventEmitter
{
    private $handler_class;

    /**
     * EventEmitter constructor.
     * @param $handler_class
     */
    public function __construct($handler_class)
    {
        $this->handler_class = $handler_class;
    }


    /**
     * @param $method
     * @param array $params
     */
    public function emmit($method, $params = [])
    {
        $obj = new $this->handler_class;
        call_user_func_array([$obj, $method], ['params'=>$params]);
    }

}