<?php

use Rwcoding\Pscc\Di;
use Rwcoding\Pscc\Core\ContextFactory;
use Rwcoding\Pscc\Exception\WhoopsPrettyPageHandler;
use Rwcoding\Examples\Pscc\ApiContext;

require __DIR__."/../../vendor/autoload.php";

$di = Di::my();
$di->exception->addHandler(new WhoopsPrettyPageHandler());
try {
    $di->init(require __DIR__."/../config/main.php");
    $di->app->run(ContextFactory::webContext(ApiContext::class));
} catch (\Throwable $e) {
    $di->exception->handle($e);
    foreach ($di->exception->getResult() as $item) {
        echo $item;
    }
}