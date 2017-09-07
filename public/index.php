<?php
# 引入Composer自动加载
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;

# 通用控制器,解耦模板渲染
function render_template(Request $request)
{
    # 和请求有关的一些附加参数
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__.'/../resources/views/%s.php', $_route);

    return new Response(ob_get_clean()); 
}

$request = Request::createFromGlobals();

# 引入路由配置
$routes = include __DIR__.'/../routes/web.php';
# 根据路由配置匹配请求的URL路径
$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

# 控制器解析器和控制器参数解析器
$controllerResolver = new HttpKernel\Controller\ControllerResolver();
$argumentResolver = new HttpKernel\Controller\ArgumentResolver();

# 模板判断改为异常判断
try {
    # 添加附加请求参数,即pathinfo的数组形式.
    $request->attributes->add($matcher->match($request->getPathInfo()));
    # 这里是HttpKernel组件约定好的_controller回调或签名
    # 控制器解析器根据请求获取签名对象和方法或回调
    $_controller = $controllerResolver->getController($request);
    # 控制器参数解析器
    $_arguments = $argumentResolver->getArguments($request, $_controller);
    # 回调控制器方法或匿名函数,他们都在路由文件中.
    $response = call_user_func_array($_controller ,$_arguments);
} catch (Routing\Exception\ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
} catch (Exception $e) {
    $response = new Response('An error occurred', 500);
}

# 响应给浏览器
$response->send();
