<?php

namespace henrik\events;

/**
 * Class EventActions.
 */
class EventActions
{
    public const CHANGE_ACTION = 'change';
    public const SELECT_ACTION = 'select';
    public const ERROR_ACTION  = 'error';

    private string $eventClass;

    private string $method;

    /**
     * @var array
     */
    private array $executableMethods = [];

    /**
     * EventActions constructor.
     *
     * @param string $eventClass
     * @param string $method
     */
    public function __construct(string $eventClass, string $method)
    {
        $this->eventClass = $eventClass;
        $this->method     = $method;
    }

    public function executeBefore(...$args): void
    {
        $args                                = func_get_args();
        $this->executableMethods['before'][] = $this->parseArray($args);
    }

    public function executeAfter(...$args): void
    {
        $args                               = func_get_args();
        $this->executableMethods['after'][] = $this->parseArray($args);
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->executableMethods;
    }

    /**
     * @param $args
     *
     * @return array
     */
    private function parseArray($args): array
    {
        foreach ($args as $param) {
            $method   = $param;
            $priority = 10;

            if (is_array($param)) {
                if (isset($param[0])) {
                    $method = $param[0];
                }
                if (isset($param[1]) && is_integer($param[1])) {
                    $priority = $param[1];
                }
            }

            return [
                'class'    => $this->eventClass,
                'method'   => $method,
                'priority' => $priority,
            ];
        }
        return [];
    }
}