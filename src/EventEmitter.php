<?php

namespace henrik\events;

/**
 * Class EventEmitter.
 */
class EventEmitter
{
    private string $handlerClass;

    /**
     * EventEmitter constructor.
     *
     * @param string $handlerClass
     */
    public function __construct(string $handlerClass)
    {
        $this->handlerClass = $handlerClass;
    }

    /**
     * @param string $method
     * @param array $params
     */
    public function emmit(string $method, array $params = []): void
    {
        /** @var object $obj */
        $obj = new $this->handlerClass();
        call_user_func_array([$obj, $method], ['params' => $params]);
    }
}