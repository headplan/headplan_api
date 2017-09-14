<?php
# 引入Composer自动加载
require_once __DIR__.'/../bootstrap/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel;
use Headplan\Framework;

$request = Request::createFromGlobals();

# 引入路由配置
$routes = include __DIR__.'/../routes/web.php';

# 实例化框架
$framework = new Framework($routes);
# HTTP缓存
$framework = new HttpKernel\HttpCache\HttpCache(
    $framework,
    new HttpKernel\HttpCache\Store(__DIR__.'/../storage/cache'),
    new HttpKernel\HttpCache\Esi(),
    [
        'debug' => true,
    ]
);

# 响应给浏览器
$framework->handle($request)->send();