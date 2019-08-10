<?php


namespace event\core;

/**
 * Class EventActions
 * @package event
 */
class EventActions
{
    /**
     *
     */
    const CHANGE_ACTION = 'change';
    /**
     *
     */
    const SELECT_ACTION = 'select';
    /**
     *
     */
    const ERROR_ACTION = 'error';

    /**
     * @var
     */
    private $event_class;
    /**
     * @var
     */
    private $method;

    /**
     * @var array
     */
    private $executable_methods = [];

    /**
     * EventActions constructor.
     * @param $event_class
     * @param $method
     */
    public function __construct($event_class, $method)
    {
        $this->event_class = $event_class;
        $this->method = $method;
    }


    /**
     *
     */
    public function executeBefore(/* ..$args */)
    {
        $args = func_get_args();
        $this->executable_methods['before'][] = $this->parseArray($args);
    }

    /**
     *
     */
    public function executeAfter(/* ..$args */)
    {
        $args = func_get_args();
        $this->executable_methods['after'][] = $this->parseArray($args);
    }

    /**
     *
     */
    public function executeThreadedAfter(/*...$args*/)
    {
        $args = func_get_args();
        $this->executable_methods['threadedAfter'][] = $this->parseArray($args, true);
    }

    /**
     *
     */
    public function executeThreadedBefore(/*...$args*/)
    {
        $args = func_get_args();
        $this->executable_methods['threadedBefore'][] = $this->parseArray($args, true);
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->executable_methods;
    }

    /**
     * @param $args
     * @param bool $is_threaded
     * @return array
     */
    private function parseArray($args, bool $is_threaded = false)
    {
        foreach ($args as $param) {
            $method = $param;
            $priority = 10;

            if (is_array($param)) {
                if (isset($param[0])) {
                    $method = $param[0];
                }
                if (isset($param[1]) && is_integer($param[1])) {
                    $priority = $param[1];
                }
            }
            if ($is_threaded) {
                return [
                    'class' => ThreadedEventActions::class,
                    'event_class_params' => [
                        'class' => $this->event_class,
                        'method' => $method
                    ],
                    'method' => 'start',
                    'priority' => $priority
                ];
            }
            return [
                'class' => $this->event_class,
                'method' => $method,
                'priority' => $priority
            ];
        }
    }
}