<?php
# 引入Composer自动加载
require_once __DIR__.'/../bootstrap/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Headplan\Framework;

$request = Request::createFromGlobals();

# 引入路由配置
$routes = include __DIR__.'/../routes/web.php';

# 根据路由配置匹配请求的URL路径
$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
# 控制器解析器和控制器参数解析器
$controllerResolver = new HttpKernel\Controller\ControllerResolver();
$argumentResolver = new HttpKernel\Controller\ArgumentResolver();

# 创建dispatcher并注册一个监听到response事件
$routes = include __DIR__.'/../config/listeners.php';

# 实例化框架
$framework = new Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);
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