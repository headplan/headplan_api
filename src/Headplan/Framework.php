<?php
namespace Headplan;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Headplan\Events\ResponseEvent;

class Framework
{
    # 派遣器
    private $dispatcher;
    # 路由匹配器
    private $matcher;
    # 控制器解析器
    private $controllerResolver;
    # 参数解析器
    private $argumentResolver;

    # 初始化
    public function __construct(
        EventDispatcher $dispatcher,
        UrlMatcherInterface $matcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver)
    {
        $this->dispatcher = $dispatcher;
        $this->matcher = $matcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }
    
    public function handle(Request $request)
    {
        $this->matcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not Found', 404);
        } catch (\Exception $e) {
            $response = new Response('An error occurred', 500);
        }

        # 在响应前派遣事件
        $this->dispatcher->dispatch('response', new ResponseEvent($request, $response));

        return $response;
    }
}
