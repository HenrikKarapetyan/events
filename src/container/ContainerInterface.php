<?php
/**
 * Created by PhpStorm.
 * User: Henrik
 * Date: 3/31/2018
 * Time: 1:47 PM
 */

namespace event\container;


/**
 * Interface ContainerInterface
 * @package event\container
 */
interface ContainerInterface extends \Psr\Container\ContainerInterface
{
    /**
     * @param string $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $id
     * @param $value
     * @return mixed
     */
    public function set($id, $value);

    /**
     * @param $id
     * @return boolean
     */
    public function has($id);
    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * @return mixed
     */
    public function deleteAll();

    // public function update($id, $new_value);
}