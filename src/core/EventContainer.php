<?php


namespace henrik\events\core;


use henrik\container\Container;
use henrik\container\ContainerModes;

/**
 * Class EventContainer
 * @package event
 */
class EventContainer extends Container
{

    /**
     * EventContainer constructor.
     * @throws \henrik\container\UndefinedModeException
     */
    public function __construct()
    {
        $this->change_mode(ContainerModes::MULTIPLE_VALUE_MODE);
    }
}