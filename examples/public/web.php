<?php

use Rwcoding\Pscc\Core\Context;
use Rwcoding\Pscc\Di;
use Rwcoding\Pscc\Core\ContextFactory;
use Rwcoding\Pscc\Core\ControllerInterface;
use Rwcoding\Pscc\Exception\WhoopsPrettyPageHandler;

require __DIR__."/../vendor/autoload.php";

$di = Di::my();
$di->exception->addHandler(new WhoopsPrettyPageHandler());
try {
    $di->init([
        "locale_path"     => __DIR__."/resources/lang",
        "locale"          => "zh-CN",
        "locale_fallback" => "en",
    ]);
    $di->router->add("/", [TestController::class, "index"]);
    $di->router->add("/user", [TestController::class, "user"]);
    $di->app->run(ContextFactory::webContext());
} catch (\Throwable $e) {
    $di->exception->handle($e);
}