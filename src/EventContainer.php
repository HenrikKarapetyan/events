<?php

namespace henrik\events;

use henrik\container\Container;
use henrik\container\ContainerModes;
use henrik\container\exceptions\UndefinedModeException;

/**
 * Class EventContainer.
 */
class EventContainer extends Container
{
    /**
     * EventContainer constructor.
     *
     * @throws UndefinedModeException
     */
    public function __construct()
    {
        $this->changeMode(ContainerModes::MULTIPLE_VALUE_MODE);
    }
}