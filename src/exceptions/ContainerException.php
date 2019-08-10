<?php
/**
 * Created by PhpStorm.
 * User: Henrik
 * Date: 4/1/2018
 * Time: 8:40 AM
 */

namespace event\exceptions;


use Psr\Container\ContainerExceptionInterface;

/**
 * Class ContainerException
 * @package event\exceptions
 */
class ContainerException  extends EventException implements ContainerExceptionInterface
{
}