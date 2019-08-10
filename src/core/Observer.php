<?php


namespace henrik\events\core;

use henrik\events\interfaces\ObserverInterface;

/**
 * Class Observer
 * @package event
 */
class Observer implements ObserverInterface
{
    /**
     * @param string $class
     * @return EventEmitter
     */
    public function handler(string $class): EventEmitter
    {
        return new EventEmitter($class);
    }
}