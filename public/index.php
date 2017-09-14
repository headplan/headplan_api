<?php
# 引入Composer自动加载
require_once __DIR__.'/../bootstrap/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Headplan\Framework;

$request = Request::createFromGlobals();
# 实例化一个请求堆栈
$requestStack = new RequestStack();

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

# RouterListener实现和框架相同的逻辑,匹配进入的请求,再以路由参数来装载请求的属性
$dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));

$listener = new HttpKernel\EventListener\ExceptionListener(
    'Headplan\\Framework\\Controllers\\ErrorController::exceptionAction'
);
$dispatcher->addSubscriber($listener);
# 确保响应兼容HTTP协议
$dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));
# 支持流响应
$dispatcher->addSubscriber(new HttpKernel\EventListener\StreamedResponseListener());
# 返回字符串
$dispatcher->addSubscriber(new Headplan\Events\StringResponseListener());

# 实例化框架
$framework = new Framework($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
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