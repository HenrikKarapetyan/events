<?php


namespace henrik\events;


use henrik\events\core\EventActions;
use henrik\events\core\EventContainer;
use henrik\events\core\Observer;
use henrik\events\core\ThreadedEventActions;
use henrik\events\exceptions\IdAlreadyExistsException;
use henrik\events\exceptions\TypeException;
use henrik\events\exceptions\UncompatibleClassTypeException;
use Pool;

/**
 * Class EventProcessor
 * @package event
 */
class EventProcessor
{

    /**
     * @var EventProcessor
     */
    private static $instance;
    /**
     * @var EventContainer
     */
    private $container;

    /**
     * EventProcessor constructor.
     */
    public function __construct()
    {
        $this->container = new EventContainer();
    }

    /**
     * @return EventProcessor
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }


    /**
     * @param $id
     * @param $action
     * @param $params
     * @throws \Exception
     */
    public function handleAction($id, $action, $params)
    {
        /**
         * @var $emitters array[EventEmitter]
         */
        $emitters = $this->container->get($id);
        foreach ($emitters as $emitter) {
            $method = 'handle' . ucfirst($action);
            $emitter->emmit($method, $params);
        }
    }


    /**
     * @param string $event_class
     * @param string $action
     * @throws \Exception
     */
    public function emmit(string $event_class, string $action)
    {
        $actions = $this->container->get($event_class);
        foreach ($actions as $event_action) {
            $this->startBeforeActions($event_action);
            $this->startThreadedBeforeActions($event_action);
            call_user_func_array([$event_class, $action], []);
            $this->startAfterActions($event_action);
            $this->startThreadedAfterActions($event_action);
        }
    }

    /**
     * @param string $event_class
     * @param string $method
     * @param \Closure $callback
     * @throws \henrik\container\exceptions\IdAlreadyExistsException
     * @throws \henrik\container\exceptions\TypeException
     */
    public function addEvent(string $event_class, string $method, \Closure $callback)
    {
        $eventActions = new EventActions($event_class, $method);
        $callback($eventActions);
        $methods = $eventActions->getMethods();
        $this->container->set($event_class,
            [
                'method' => $method,
                'actions' => $methods
            ]
        );
    }

    /**
     * @param string $event_id
     * @param string $handler
     * @throws \henrik\container\exceptions\IdAlreadyExistsException
     * @throws \henrik\container\exceptions\TypeException
     */
    public function eventOutOfScopeStateChange(string $event_id, string $handler)
    {
        $observer = new Observer();
        try {
            $emitter = $observer->handler($handler);
            $this->container->set($event_id, $emitter);
        } catch (UncompatibleClassTypeException $e) {
        } catch (IdAlreadyExistsException $e) {
        } catch (TypeException $e) {
        }
    }


    /**
     * @param array $action
     */
    private function startBeforeActions(array $action)
    {
        if (!empty($action['actions']['before'])) {
            foreach ($action['actions']['before'] as $before_action) {
                $class = $before_action['class'];
                $method = $before_action['method'];
                call_user_func_array([$class, $method], []);
            }
        }
    }

    /**
     * @param $action
     */
    private function startThreadedBeforeActions($action)
    {
        if (!empty($action['actions']['threadedBefore'])) {
            foreach ($action['actions']['threadedBefore'] as $before_threaded) {
                $this->runThread($before_threaded);
            }
        }
    }

    /**
     * @param array $action
     */
    private function startAfterActions(array $action)
    {
        if (!empty($action['actions']['after'])) {
            foreach ($action['actions']['after'] as $after_action) {
                $class = $after_action['class'];
                $method = $after_action['method'];
                call_user_func_array([$class, $method], []);
            }
        }
    }

    /**
     * @param array $action
     */
    private function startThreadedAfterActions(array $action)
    {
        if (!empty($action['actions']['threadedAfter'])) {
            foreach ($action['actions']['threadedAfter'] as $before_threaded) {
                $this->runThread($before_threaded);
            }
        }
    }

    /**
     * @param $threaded_conf
     */
    private function runThread($threaded_conf)
    {
        /**
         * @var $thread_obj ThreadedEventActions
         */
        $class = $threaded_conf['class'];
        $method = $threaded_conf['method'];
        $ev_obj = new $threaded_conf['event_class_params']['class'];

        /**
         * initialising Threaded  event class
         */
        $pool = new Pool(4);

        $thread_obj = new $class;
        $thread_obj->setParams($ev_obj, $threaded_conf['event_class_params']['method']);
        $thread_obj->$method();
//        $thread_obj->join();
    }
}