<?php

namespace App\Listeners;

use Headplan\Events\ResponseEvent;
use Headplan\Events\Listeners;

class ContentListener
{
    public function onEvent(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $headers = $response->headers;

        if (!$headers->has('Content-Length') && !$headers->has('Transfer-Encoding')) {
            $headers->set('Content-Length', strlen($response->getContent()));
            $headers->set('Content-type', 'text/plain');
        }
    }
}