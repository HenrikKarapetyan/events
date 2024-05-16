<?php
/**
 * Created by PhpStorm.
 * User: Henrik
 * Date: 4/1/2018
 * Time: 8:37 AM.
 */

namespace henrik\events\exceptions;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ServiceNotFoundException.
 */
class ServiceNotFoundException extends EventException implements NotFoundExceptionInterface {}