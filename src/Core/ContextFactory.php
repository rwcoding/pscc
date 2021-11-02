<?php

namespace Rwcoding\Pscc\Core;

use Rwcoding\Pscc\Core\Console\ConsoleRequest;
use Rwcoding\Pscc\Core\Console\ConsoleResponse;
use Rwcoding\Pscc\Core\Web\Request;
use Rwcoding\Pscc\Core\Web\RequestSwoole;
use Rwcoding\Pscc\Core\Web\Response;
use Rwcoding\Pscc\Core\Web\ResponseSwoole;

class ContextFactory
{
    public static function consoleContext(string $class = ""): Context
    {
        if ($class) {
            return new $class(new ConsoleRequest(), new ConsoleResponse());
        }
        return new Context(new ConsoleRequest(), new ConsoleResponse());
    }

    public static function webContext(string $class = ""): Context
    {
        if ($class) {
            return new $class(new Request(), new Response());
        }
        return new Context(new Request(), new Response());
    }

    public static function swooleWebContext(\Swoole\Http\Request $request, \Swoole\Http\Response $response, string $class = ""): Context
    {
        if ($class) {
            return new $class(new RequestSwoole($request), new ResponseSwoole($response));
        }
        return new Context(new RequestSwoole($request), new ResponseSwoole($response));
    }
}