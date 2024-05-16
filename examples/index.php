<?php

require '../vendor/autoload.php';

use henrik\container\exceptions\IdAlreadyExistsException;
use henrik\events\EventActions;
use henrik\events\EventProcessor;
use henrik\events\Samples\AfterBeforeMethods;
use henrik\sl\DependencyInjector;
use henrik\sl\InjectorModes;

$dependencyInjector = DependencyInjector::instance();
$dependencyInjector->setMode(InjectorModes::AUTO_REGISTER);

/** @var EventProcessor $eventProcessor */
$eventProcessor = $dependencyInjector->get(EventProcessor::class);

try {
    $eventProcessor->addEvent(
        AfterBeforeMethods::class,
        'save',
        function (EventActions $actions) {
            $actions->executeBefore(['beforeSave', 1]);
            $actions->executeAfter(['afterSave', 12]);
        }
    );
} catch (IdAlreadyExistsException $e) {
}

try {
    $eventProcessor->emmit(AfterBeforeMethods::class, 'save');
} catch (Exception $e) {
}
