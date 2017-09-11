<?php

namespace App\Listeners;

use Symfony\Component\EventDispatcher\EventDispatcher;

# 创建dispatcher并注册一个监听到response事件
$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new CodeListener(['response', -1]));
$dispatcher->addListener('response', [new ContentListener, 'onEvent']);