<?php
/**
 * Created by PhpStorm.
 * User: Henrik
 * Date: 4/1/2018
 * Time: 8:40 AM
 */

namespace henrik\events\exceptions;


use Psr\Container\ContainerExceptionInterface;

/**
 * Class ContainerException
 * @package henrik\events\exceptions
 */
class ContainerException  extends EventException implements ContainerExceptionInterface
{
}