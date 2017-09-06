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

function is_leap_year($year = null)
{
    if (null === $year) {
        $year = date('Y');
    }

    return 0 === $year % 400 || (0 === $year % 4 && 0 !== $year % 100);
}

class LeapYearController
{
    public function indexAction($request)
    {
        if (is_leap_year($request->attributes->get('year'))) {
            return new Response('是闰年!'.'现在时间是:'.date('Y-m-d H:i:s', time()));
        }

        return new Response('不是闰年!'.'现在时间是:'.date('Y-m-d H:i:s', time()));
    }
}

$routes->add('leap-year', new Routing\Route('/leap-year/{year}', [
    'year' => null,
    '_controller' => array(new LeapYearController(), 'indexAction')
]));

return $routes;
