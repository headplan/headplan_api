<?php
# 引入Composer自动加载
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();
$response = new Response();

# 文件map
$map = [
    '/hello' => 'hello',
    '/about' => 'about'
];

# 获取Pathinfo
$path = $request->getPathInfo();
# 判断文件是否存在
if (isset($map[$path])) {
    # 存在则引入,打开缓冲区并获取缓冲区的内容给响应
    ob_start();
    # 导入全局数组中的变量
    extract($request->query->all(), EXTR_SKIP);
    # 更统一的引入
    include sprintf(__DIR__.'/../resources/views/%s.php', $map[$path]);;
    $response->setContent(ob_get_clean());
} else {
    # 不存在则调整响应信息
    $response->setStatusCode(404);
    $response->setContent('Not Found');
}

# 响应给浏览器
$response->send();