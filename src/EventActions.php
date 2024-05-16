<?php

namespace henrik\events;

/**
 * Class EventActions.
 */
class EventActions
{
    private string $eventClass;

    private string $method;

    /**
     * @var array<string, array<int|string, array<string, int|string>|int|string>>
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

    public function executeBefore(string $method, int $priority): void
    {
        $this->executableMethods['before'][] = [
            'class'    => $this->eventClass,
            'method'   => $method,
            'priority' => $priority,
        ];
    }

    public function executeAfter(string $method, int $priority): void
    {
        $this->executableMethods['after'][] = [
            'class'    => $this->eventClass,
            'method'   => $method,
            'priority' => $priority,
        ];
    }

    /**
     * @return array<string, array<int|string, array<string, int|string>|int|string>>
     */
    public function getMethods(): array
    {
        return $this->executableMethods;
    }


}