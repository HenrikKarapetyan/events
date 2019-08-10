<?php


namespace event\core;


use event\exceptions\UncompatibleClassTypeException;
use event\handlers\OutOfScopeEventHandler;
use event\interfaces\EventHandlerInterface;
use event\interfaces\ObserverInterface;

/**
 * Class Observer
 * @package event
 */
class Observer implements ObserverInterface
{
    /**
     * @param string $class
     * @return EventEmitter|mixed
     * @throws UncompatibleClassTypeException
     */
    public function handler(string $class)
    {
        if (in_array(OutOfScopeEventHandler::class, class_uses((new $class)))){
            return new EventEmitter($class);
        }
        throw new UncompatibleClassTypeException("The {$class} must implement `event\interfaces\EventHandlerInterface` interface");
    }
}