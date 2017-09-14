<?php

namespace Headplan;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\StreamedResponseListener;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\HttpKernel;

class Framework extends HttpKernel
{
    public function __construct($routes)
    {
        # 实例化一个请求堆栈
        $requestStack = new RequestStack();

        # 根据路由配置匹配请求的URL路径
        $context = new RequestContext();
        $matcher = new UrlMatcher($routes, $context);

        # 控制器解析器和控制器参数解析器
        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        # 订阅事件
        $dispatcher = new EventDispatcher();
        # RouterListener实现匹配进入的请求,再以路由参数来装载请求的属性
        $dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));
        # 确保响应兼容HTTP协议
        $dispatcher->addSubscriber(new ResponseListener('UTF-8'));
        # 支持流响应
        $dispatcher->addSubscriber(new StreamedResponseListener());
        # 返回字符串
        $dispatcher->addSubscriber(new Events\StringResponseListener());
        # 自定义异常
        $listener = new ExceptionListener(
            'Headplan\\Framework\\Controllers\\ErrorController::exceptionAction'
        );
        $dispatcher->addSubscriber($listener);

        parent::__construct($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
    }
}
