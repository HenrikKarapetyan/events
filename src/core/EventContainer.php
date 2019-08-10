<?php


namespace event\core;


use event\container\Container;
use event\container\ContainerModes;

/**
 * Class EventContainer
 * @package event
 */
class EventContainer extends Container
{

    /**
     * EventContainer constructor.
     * @throws \event\exceptions\ContainerModeException
     */
    public function __construct()
    {
        parent::__construct();
        $this->change_mode(ContainerModes::MULTIPLE_VALUE_MODE);
    }
}