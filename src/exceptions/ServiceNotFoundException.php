<?php
/**
 * Created by PhpStorm.
 * User: Henrik
 * Date: 4/1/2018
 * Time: 8:37 AM
 */

namespace event\exceptions;


use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ServiceNotFoundException
 * @package event\exceptions
 */
class ServiceNotFoundException extends EventException implements NotFoundExceptionInterface
{

}