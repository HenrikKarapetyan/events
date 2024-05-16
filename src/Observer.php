<?php

namespace henrik\events;

use henrik\events\Interfaces\ObserverInterface;

/**
 * Class Observer.
 */
class Observer implements ObserverInterface
{
    /**
     * @param string $class
     *
     * @return EventEmitter
     */
    public function handler(string $class): EventEmitter
    {
        return new EventEmitter($class);
    }
}