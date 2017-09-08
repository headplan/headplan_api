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

# 实例化框架
$framework = new Framework($matcher, $controllerResolver, $argumentResolver);
$response = $framework->handle($request);

# 响应给浏览器
$response->send();
