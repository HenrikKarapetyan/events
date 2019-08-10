<?php
/**
 * Created by PhpStorm.
 * User: Henrik
 * Date: 2/7/2018
 * Time: 12:53 PM
 */

namespace event\core;

use event\EventActions;
use event\EventProcessor;
use event\exceptions\InvalidCallException;
use event\exceptions\UnknownPropertyException;

/**
 * Class Component
 * @package event\core
 */
trait Component
{

    /**
     * @var
     */
    private $event_processor;
    /**
     * @return string
     */
    public function getClassName()
    {
        return get_called_class();
    }

    /**
     * @param $name
     * @return mixed
     * @throws InvalidCallException
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            $this->send_action(EventActions::SELECT_ACTION, ['name' => $name]);
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        }

        throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * @param $name
     * @param $value
     * @throws InvalidCallException
     * @throws UnknownPropertyException
     */
    public function __set($name, $value)
    {
        $old_value = $this->$name;
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->send_action(EventActions::CHANGE_ACTION, ['name' => $name, 'old_value' => $old_value, 'new_value' => $value]);
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }


    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }

    /**
     * @param $name
     * @throws InvalidCallException
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Unseting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * @param $name
     * @param $params
     * @throws UnknownMethodException
     */
    public function __call($name, $params)
    {
        throw new UnknownMethodException('Calling unknown method: ' . get_class($this) . "::$name()");
    }


    /**
     * @param $name
     * @param bool $checkVars
     * @return bool
     */
    public function hasProperty($name, $checkVars = true)
    {
        return $this->canGetProperty($name, $checkVars) || $this->canSetProperty($name, false);
    }

    /**
     * @param $name
     * @param bool $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name);
    }

    /**
     * @param $name
     * @param bool $checkVars
     * @return bool
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'set' . $name) || $checkVars && property_exists($this, $name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }

    /**
     * @param $action
     * @param array $params
     * @throws \Exception
     */
    private function send_action($action, $params = [])
    {
        if (is_null($this->event_processor)) {
            $this->event_processor = EventProcessor::getInstance();
        }
        $this->event_processor->handleAction(get_class($this), $action, $params);
    }
}