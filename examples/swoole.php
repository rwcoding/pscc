<?php

use Rwcoding\Pscc\Netio\Http;
use Rwcoding\Pscc\Netio\EventInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Rwcoding\Pscc\Bootstrap;
use Rwcoding\Pscc\Di;
use Rwcoding\Pscc\Exception\WhoopsPrettyPageHandler;
use Swoole\Server;
use Rwcoding\Pscc\Core\ContextFactory;
use Rwcoding\Examples\Pscc\ApiContext;
use Rwcoding\Examples\Pscc\Hook;

require __DIR__."/../vendor/autoload.php";

$s = new Bootstrap();
$s->register(new class extends Http implements EventInterface {

    public function onWorkerStart(Server $server, int $workerId)
    {
        $di = Di::my();
        $di->set("ss", $server);
        $di->exception->addHandler(new WhoopsPrettyPageHandler());
        $di->init(require __DIR__."/config/main.php");
    }

    public function onRequest(Request $request, Response $response)
    {
        $di = Di::my();
        try {
            $di->app->run(ContextFactory::swooleWebContext($request, $response, ApiContext::class));
            Hook::over();
        } catch (\Throwable $e) {
            $di->exception->handle($e);
            $response->end($di->exception->getResult(true)[0]);
        }
    }
});
$s->run();