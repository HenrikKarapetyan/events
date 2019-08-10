<?php
/**
 * Created by PhpStorm.
 * User: Henrik
 * Date: 3/31/2018
 * Time: 10:34 AM
 */

namespace event\container;


use event\exceptions\ContainerModeException;
use event\exceptions\IdAlreadyExistsException;
use event\exceptions\ServiceNotFoundException;
use event\exceptions\TypeException;
use event\core\Component;

/**
 * Class Container
 * @package event\container
 */
class Container implements ContainerInterface
{


    use Component;
    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var integer
     */
    private $mode;

    /**
     * Container constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->data[$id];
        }
        throw new ServiceNotFoundException(sprintf('service by "%s" id not found', $id));
    }

    /**
     * @param $id
     * @return boolean
     */
    public function has($id)
    {
        return isset($this->data[$id]);
    }

    /**
     * @param $id
     * @param $value
     * @return mixed|void
     * @throws IdAlreadyExistsException
     * @throws TypeException
     */
    public function set($id, $value)
    {
        if (is_string($id)) {
            if ($this->mode == ContainerModes::SINGLE_VALUE_MODE) {
                if ($this->has($id)) {
                    throw new IdAlreadyExistsException(sprintf('"%s" id is already exists please choose another name', $id));
                } else {
                    $this->data[$id] = $value;
                }
            } else {
                $this->data[$id][] = $value;
            }

        } else {
            throw new TypeException(sprintf('id must  be type of string %s given', gettype($id)));
        }
    }

    /**
     * @param $id
     * @return void
     */
    public function delete($id)
    {
        unset($this->data[$id]);
    }

    /**
     * @return void
     */
    public function deleteAll()
    {
        $this->data = [];
    }

    /**
     * @param $mode
     * @throws ContainerModeException
     */
    public function change_mode($mode)
    {
        if (!in_array($mode, ContainerModes::MODES)) {
            throw new ContainerModeException('The {$mode} mode not exists');
        }
        $this->mode = $mode;
    }

}