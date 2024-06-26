<?php

namespace Henrik\Events;

use Closure;
use Henrik\Contracts\EventDispatcherInterface;
use Henrik\Contracts\EventSubscriberInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /** @var mixed */
    private array $listeners = [];

    /** @var mixed */
    private array $sorted = [];
    /** @var mixed */
    private array $optimized;

    public function __construct()
    {
        if (static::class === __CLASS__) {
            $this->optimized = [];
        }
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $eventName ??= $event::class;
        $listeners = $this->getListeners($eventName);

        if (isset($this->optimized)) {
            $listeners = $this->optimized[$eventName] ?? (empty($this->listeners[$eventName]) ? [] : $this->optimizeListeners($eventName));
        }

        if ($listeners) {
            $this->callListeners($listeners, $eventName, $event);
        }

        return $event;
    }

    /**
     * @param string|null $eventName
     *
     * @return callable|callable[]|array<mixed>
     */
    public function getListeners(?string $eventName = null): array
    {
        if ($eventName !== null) {
            if (empty($this->listeners[$eventName])) {
                return [];
            }

            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }

            return $this->sorted[$eventName];
        }

        foreach ($this->listeners as $eventName => $eventListeners) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
        }

        return array_filter($this->sorted);
    }

    public function getListenerPriority(string $eventName, array|callable $listener): ?int
    {
        if (empty($this->listeners[$eventName])) {
            return null;
        }

        if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof Closure && 2 >= \count($listener)) {
            $listener[0] = $listener[0]();
            $listener[1] ??= '__invoke';
        }

        foreach ($this->listeners[$eventName] as $priority => &$listeners) {
            foreach ($listeners as &$v) {
                if ($v !== $listener && \is_array($v) && isset($v[0]) && $v[0] instanceof Closure && 2 >= \count($v)) {
                    $v[0] = $v[0]();
                    $v[1] ??= '__invoke';
                }
                if ($v === $listener || ($listener instanceof Closure && $v == $listener)) {
                    return $priority;
                }
            }
        }

        return null;
    }

    public function hasListeners(?string $eventName = null): bool
    {
        if ($eventName !== null) {
            return !empty($this->listeners[$eventName]);
        }

        foreach ($this->listeners as $eventListeners) {
            if ($eventListeners) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string                         $eventName
     * @param callable|array<object, string> $listener
     * @param int                            $priority
     *
     * @return void
     */
    public function addListener(string $eventName, array|callable $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName], $this->optimized[$eventName]);
    }

    /**
     * @param string                         $eventName
     * @param array<object, string>|callable $listener
     *
     * @return void
     */
    public function removeListener(string $eventName, array|callable $listener): void
    {
        if (empty($this->listeners[$eventName])) {
            return;
        }

        if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof Closure && 2 >= \count($listener)) {
            $listener[0] = $listener[0]();
            $listener[1] ??= '__invoke';
        }

        foreach ($this->listeners[$eventName] as $priority => &$listeners) {
            foreach ($listeners as $k => &$v) {
                if ($v !== $listener && \is_array($v) && isset($v[0]) && $v[0] instanceof Closure && 2 >= \count($v)) {
                    $v[0] = $v[0]();
                    $v[1] ??= '__invoke';
                }
                if ($v === $listener || ($listener instanceof Closure && $v == $listener)) {
                    unset($listeners[$k], $this->sorted[$eventName], $this->optimized[$eventName]);
                }
            }

            if (!$listeners) {
                unset($this->listeners[$eventName][$priority]);
            }
        }
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (\is_string($params)) {
                $this->addListener($eventName, [$subscriber, $params]);

                return;
            }
            if (\is_string($params[0])) {
                /** @var int $priority */
                $priority = $params[1] ?? 0;
                $this->addListener($eventName, [$subscriber, $params[0]], $priority);

                return;
            }

            foreach ($params as $listener) {
                /** @var int $priority */
                $priority = $listener[1] ?? 0;
                $this->addListener($eventName, [$subscriber, $listener[0]], $priority);
            }
        }
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {

            if (\is_array($params) && \is_array($params[0])) {

                foreach ($params as $listener) {
                    $this->removeListener($eventName, [$subscriber, $listener[0]]);
                }

                return;
            }

            $this->removeListener($eventName, [$subscriber, \is_string($params) ? $params : $params[0]]);
        }
    }

    /**
     * Triggers the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners The event listeners
     * @param string     $eventName The name of the event to dispatch
     * @param object     $event     The event object to pass to the event handlers/listeners
     */
    protected function callListeners(iterable $listeners, string $eventName, object $event): void
    {
        $stoppable = $event instanceof StoppableEventInterface;

        foreach ($listeners as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }
            $listener($event, $eventName, $this);
        }
    }

    /**
     * Sorts the internal list of listeners for the given event by priority.
     *
     * @param string $eventName
     */
    private function sortListeners(string $eventName): void
    {
        krsort($this->listeners[$eventName]);
        $this->sorted[$eventName] = [];

        foreach ($this->listeners[$eventName] as &$listeners) {
            foreach ($listeners as &$listener) {
                if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof Closure && 2 >= \count($listener)) {
                    $listener[0] = $listener[0]();
                    $listener[1] ??= '__invoke';
                }
                $this->sorted[$eventName][] = $listener;
            }
        }
    }

    /**
     * @param string $eventName
     *
     * @return array
     */
    private function optimizeListeners(string $eventName): array
    {
        krsort($this->listeners[$eventName]);
        $this->optimized[$eventName] = [];
        foreach ($this->listeners[$eventName] as &$listeners) {
            foreach ($listeners as &$listener) {

                $closure = &$this->optimized[$eventName][];
                if (\is_array($listener) && isset($listener[0]) && $listener[0] instanceof Closure && 2 >= \count($listener)) {

                    $closure = static function (...$args) use (&$listener, &$closure) {
                        if ($listener[0] instanceof Closure) {
                            $listener[0] = $listener[0]();
                            $listener[1] ??= '__invoke';
                        }

                        ($closure = $listener(...))(...$args);
                    };
                } else {
                    $closure = $listener(...);
                }

            }
        }

        return $this->optimized[$eventName];
    }
}