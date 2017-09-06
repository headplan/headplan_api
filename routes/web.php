<?php
use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();

$routes->add('hello', new Routing\Route('/hello/{name}', array('name' => 'World')));
$routes->add('about', new Routing\Route('/about'));

return $routes;
