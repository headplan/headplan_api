<?php
# 引入Composer自动加载
require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

# createFromGlobals()方法基于当前PHP全局变量创建了一个Request对象
$request = Request::createFromGlobals();

$input = $request->get('name', 'World');

$response = new Response(sprintf('Hello %s', htmlspecialchars($input, ENT_QUOTES, 'UTF-8')));

#send()方法把Response对象发送回客户端(客户端首先输出HTTP头信息,然后是内容)
$response->send();
