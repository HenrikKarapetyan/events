<?php


namespace event\interfaces;


/**
 * Interface ObserverInterface
 * @package event\interfaces
 */
interface ObserverInterface
{
    /**
     * @param string $class
     * @return mixed
     */
    public function handler(string $class);
}