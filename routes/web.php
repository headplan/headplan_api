<?php

use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

# 通用控制器,解耦模板渲染
function render_template(Request $request)
{
    # 和请求有关的一些附加参数
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__.'/../resources/views/%s.php', $_route);

    return new Response(ob_get_clean());
}

$routes = new Routing\RouteCollection();

$routes->add('hello', new Routing\Route('/hello/{name}', [
    'name' => 'World',
    '_controller' => function (Request $request) {
        $response = render_template($request);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
]));

$routes->add('about', new Routing\Route('/about', [
    '_controller' => function (Request $request) {
        $request->attributes->set('test', '我是测试数据');
        $response = render_template($request);
        return $response;
    }
]));

$routes->add('leap-year', new Routing\Route('/leap-year/{year}', [
    'year' => 2012,
    '_controller' => 'App\Controllers\LeapYearController::indexAction'
]));

$routes->add('string', new Routing\Route('/string', [
    '_controller' => 'App\Controllers\StringController::indexAction'
]));

return $routes;
