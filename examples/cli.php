<?php

use Rwcoding\Pscc\Di;
use Rwcoding\Pscc\Core\ContextFactory;
use Rwcoding\Examples\Pscc\ApiContext;
use Rwcoding\Examples\Pscc\Hook;
use Rwcoding\Pscc\Exception\WhoopsPlainTextHandler;

require __DIR__."/../vendor/autoload.php";

$di = Di::my();
$di->exception->addHandler(new WhoopsPlainTextHandler);
try {
    $di->init(require __DIR__."/config/main.php");
    $di->app->run(ContextFactory::consoleContext(ApiContext::class));
    Hook::over();
} catch (\Throwable $e) {
    $di->exception->handle($e);
    foreach ($di->exception->getResult() as $item) {
        echo $item;
    }
}