<?php
use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\Response;

$routes = new Routing\RouteCollection();

$routes->add('hello', new Routing\Route('/hello/{name}', [
    'name' => 'World',
    '_controller' => function ($request) {
        $response = render_template($request);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
]));
$routes->add('about', new Routing\Route('/about', [
    '_controller' => function ($request) {
        $request->attributes->set('test', '我是测试数据');
        $response = render_template($request);
        return $response;
    }
]));

return $routes;
