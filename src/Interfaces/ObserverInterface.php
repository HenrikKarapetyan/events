<?php

namespace henrik\events\Interfaces;

use henrik\events\EventEmitter;

/**
 * Interface ObserverInterface.
 */
interface ObserverInterface
{
    /**
     * @param string $class
     *
     * @return mixed
     */
    public function handler(string $class):EventEmitter;
}