<?php

namespace Headplan\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Listeners implements EventSubscriberInterface
{
    private static $event;

    public function __construct($event)
    {
        $this->setEvent($event);
    }

    private function setEvent($event)
    {
        if (is_string($event)) {
            self::$event = [ $event => 'onEvent'];
        } elseif (is_string($event[0])) {
            self::$event = [ $event[0] => [
                'onEvent',
                isset($event[1]) ? $event[1] : 0
            ]];
        }
    }

    public static function getSubscribedEvents()
    {
        return self::$event;
    }
}