<?php

use Rwcoding\Pscc\Netio\Http;
use Rwcoding\Pscc\Netio\EventInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Rwcoding\Pscc\Bootstrap;
use Rwcoding\Pscc\Core\ContextFactory;
use Rwcoding\Pscc\Di;
use Rwcoding\Pscc\Exception\WhoopsPrettyPageHandler;
use Swoole\Server;
use Rwcoding\Pscc\Core\ControllerInterface;
use Rwcoding\Pscc\Core\Context;

require __DIR__."/../vendor/autoload.php";

class TestSwoole implements ControllerInterface
{
    private Context $context;

    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    public function index(): string
    {
        print_r($this->context->request);
        return "index";
    }

    public function user(): string
    {
        return "user";
    }
}

class Event extends Http implements EventInterface
{
    public function onWorkerStart(Server $server, int $workerId)
    {
        $di = Di::my();
        $di->exception->addHandler(new WhoopsPrettyPageHandler());
        $di->init([
            "locale_path"     => __DIR__."/resources/lang",
            "locale"          => "zh-CN",
            "locale_fallback" => "en",
        ]);
        $di->router->add("/", [TestSwoole::class, "index"]);
        $di->router->add("/user", [TestSwoole::class, "user"]);
    }

    public function onRequest(Request $request, Response $response)
    {
        $di = Di::my();
        try {
            $di->app->run(ContextFactory::swooleWebContext($request, $response));
        } catch (\Throwable $e) {
            $di->exception->handle($e);
            $response->end($di->exception->getResult(true)[0]);
        }
    }
}

$s = new Bootstrap();
$s->asHttp(new Event());
$s->run();