<?php

namespace App\Listeners;

use Headplan\Events\ResponseEvent;
use Headplan\Events\Listeners;

class CodeListener extends Listeners
{
    public function onEvent(ResponseEvent $event)
    {
        $response = $event->getResponse();

        if ($response->isRedirection()
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $event->getRequest()->getRequestFormat()
        ) {
            return;
        }

        $response->setContent($response->getContent().' ||====YO~YO~YO~');
    }
}