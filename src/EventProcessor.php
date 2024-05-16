<?php

namespace henrik\events;

use Closure;
use Exception;
use henrik\container\exceptions\IdAlreadyExistsException;
use henrik\container\exceptions\ServiceNotFoundException;
use henrik\events\exceptions\EventIdAlreadyExistsException;
use henrik\events\exceptions\TypeException;
use stdClass;

/**
 * Class EventProcessor.
 */
class EventProcessor
{
    /**
     * EventProcessor constructor.
     *
     * @param EventContainer $eventContainer
     */
    public function __construct(private readonly EventContainer $eventContainer) {}

    /**
     * @param string $id
     * @param string $action
     * @param array  $params
     *
     * @throws ServiceNotFoundException
     */
    public function handleAction(string $id, string $action, array $params)
    {
        /**
         * @var array<EventEmitter> $emitters
         */
        $emitters = $this->eventContainer->get($id);
        foreach ($emitters as $emitter) {
            $method = 'handle' . ucfirst($action);
            $emitter->emmit($method, $params);
        }
    }

    /**
     * @param string $eventClass
     * @param string $action
     *
     * @throws Exception
     */
    public function emmit(string $eventClass, string $action)
    {
        $actions = $this->eventContainer->get($eventClass);
        foreach ($actions as $eventAction) {
            $this->startBeforeActions($eventAction);
            call_user_func_array([$eventClass, $action], []);
            $this->startAfterActions($eventAction);
        }
    }

    /**
     * @param string  $eventClass
     * @param string  $method
     * @param Closure $callback
     *
     * @throws IdAlreadyExistsException
     */
    public function addEvent(string $eventClass, string $method, Closure $callback): void
    {
        $eventActions = new EventActions($eventClass, $method);
        $callback($eventActions);
        $methods = $eventActions->getMethods();
        $this->eventContainer->set(
            $eventClass,
            [
                'method'  => $method,
                'actions' => $methods,
            ]
        );
    }

    /**
     * @param string $eventId
     * @param string $handler
     *
     * @throws IdAlreadyExistsException
     */
    public function eventOutOfScopeStateChange(string $eventId, string $handler): void
    {
        $observer = new Observer();

        $emitter = $observer->handler($handler);
        $this->eventContainer->set($eventId, $emitter);
    }

    /**
     * @param array $action
     */
    private function startBeforeActions(array $action): void
    {
        if (!empty($action['actions']['before'])) {
            foreach ($action['actions']['before'] as $before_action) {
                $class  = $before_action['class'];
                $method = $before_action['method'];
                call_user_func_array([$class, $method], []);
            }
        }
    }

    /**
     * @param array<string,string|stdClass> $action
     */
    private function startAfterActions(array $action): void
    {
        if (!empty($action['actions']['after'])) {
            foreach ($action['actions']['after'] as $afterAction) {
                if (class_exists($afterAction['class'])) {
                    $class = $afterAction['class'];
                    /** @var string $method */
                    $method = $afterAction['method'];
                    call_user_func_array([$class, $method], []);
                }
            }
        }
    }
}