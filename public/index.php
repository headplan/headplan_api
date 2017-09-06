<?php
# 引入Composer自动加载
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;

# 通用控制器,解耦模板渲染
function render_template($request)
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
# 根据路由配置匹配URL路径
$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
# 模板判断改为异常判断
try {
    # 添加附加请求参数,即pathinfo的数组形式.
    $request->attributes->add($matcher->match($request->getPathInfo()));
    # 这里回调约定好的_controller匿名函数
    # 从路由配置中获取
    $_controller = $request->attributes->get('_controller');
    $response = call_user_func($_controller ,$request);
} catch (Routing\Exception\ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
} catch (Exception $e) {
    $response = new Response('An error occurred', 500);
}

# 响应给浏览器
$response->send();
