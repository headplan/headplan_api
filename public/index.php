<?php
# 引入Composer自动加载
require_once __DIR__.'/../bootstrap/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel;

# 引入容器配置
$c = include __DIR__.'/../src/Headplan/Container.php';

$c->setParameter('charset', 'UTF-8');
$c->setParameter('routes', include __DIR__.'/../routes/web.php');

$c->register('listener.string_response', 'Headplan\Events\StringResponseListener');
$c->getDefinition('dispatcher')
    ->addMethodCall('addSubscriber', [new  Symfony\Component\DependencyInjection\Reference('listener.string_response')]);


$request = Request::createFromGlobals();

# 实例化框架
$framework = $c->get('framework');
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