<?php

require "vendor/autoload.php";

use henrik\events\core\EventActions;
use henrik\events\EventProcessor;
use henrik\events\core\Event;

class AfterBeforeMethods
{
    use Event;


    public function beforeSave()
    {
        var_dump("hello from before action");
    }

    public function afterSave()
    {
        var_dump("hello from after action");
    }

    public function beforeRun()
    {
        var_dump('before run');
    }

    public function threadedBefore()
    {
        for ($i = 0; $i < 10; $i++) {
            var_dump("threadedBefore $i");
        }
    }

    public function threadedAfter()
    {
        for ($i = 0; $i < 10; $i++) {
            var_dump("threadedAfter $i");
        }
    }

    public function save()
    {
        var_dump("save");
    }
}


class SimpleEvent
{
    use Event;
    private $x;


    public function getX()
    {
        return $this->x;
    }

    public function setX($x)
    {
        $this->x = $x;
    }
}

class SimpleEventHandler
{
    public function handleChange($params)
    {
        var_dump("ok45", $params);
    }

    public function setD($value)
    {
        $this->d = $value;
    }
}


$eventProcessor = EventProcessor::getInstance();
$eventProcessor->addEvent(AfterBeforeMethods::class, 'save',
    function (EventActions $actions) {
        $actions->executeBefore(['beforeSave', 1]);
        $actions->executeAfter(['afterSave', 12]);
        $actions->executeThreadedAfter(['threadedAfter', 1]);
        $actions->executeThreadedBefore(['threadedBefore', 1]);
    }
);

$eventProcessor->emmit(AfterBeforeMethods::class, 'save');
