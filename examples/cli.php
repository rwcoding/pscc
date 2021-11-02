<?php

use Rwcoding\Pscc\Di;
use Rwcoding\Pscc\Core\ContextFactory;
use Rwcoding\Examples\Pscc\ApiContext;
use Rwcoding\Examples\Pscc\Event;

require __DIR__."/../vendor/autoload.php";

try {
    $di = Di::my();
    $di->exception->addHandler(new \Rwcoding\Pscc\Exception\WhoopsPlainTextHandler);
    $di->init(require __DIR__."/config/main.php");
    $di->app->run(ContextFactory::consoleContext(ApiContext::class));
    Event::over();
} catch (\Throwable $e) {
    $di->exception->handle($e);
}