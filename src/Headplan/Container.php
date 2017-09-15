<?php

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;

$c = new DependencyInjection\ContainerBuilder();
$c->register('context', 'Symfony\Component\Routing\RequestContext');
$c->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
    ->setArguments([
        '%routes%',
        new Reference('context')
    ]);

$c->register('request_stack', 'Symfony\Component\HttpFoundation\RequestStack');
$c->register('controller_resolver', 'Symfony\Component\HttpKernel\Controller\ControllerResolver');
$c->register('argument_resolver', 'Symfony\Component\HttpKernel\Controller\ArgumentResolver');

$c->register('listener.router', 'Symfony\Component\HttpKernel\EventListener\RouterListener')
    ->setArguments([
        new Reference('matcher'),
        new Reference('request_stack')
    ]);
$c->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
    ->setArguments([
        '%charset%'
    ]);
$c->register('listener.streamed_response', 'Symfony\Component\HttpKernel\EventListener\StreamedResponseListener');
$c->register('listener.exception', 'Symfony\Component\HttpKernel\EventListener\ExceptionListener')
    ->setArguments([
        'Calendar\\Controller\\ErrorController::exceptionAction'
    ]);

$c->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
    ->addMethodCall('addSubscriber', [new Reference('listener.router')])
    ->addMethodCall('addSubscriber', [new Reference('listener.response')])
    ->addMethodCall('addSubscriber', [new Reference('listener.streamed_response')])
    ->addMethodCall('addSubscriber', [new Reference('listener.exception')]);

$c->register('framework', 'Headplan\Framework')
    ->setArguments([
        new Reference('dispatcher'),
        new Reference('controller_resolver'),
        new Reference('request_stack'),
        new Reference('argument_resolver'),
    ]);

return $c;