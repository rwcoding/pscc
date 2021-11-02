<?php

namespace Rwcoding\Pscc\Exception;

class WhoopsPrettyPageHandler implements ExceptionHandlerInterface
{
    public function handle(\Throwable $exception): ?string
    {
        $whoops = new \Whoops\Run;
        $whoops->sendHttpCode(404);
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $handler = new \Whoops\Handler\PrettyPageHandler();
        $whoops->pushHandler($handler);
        if (PHP_SAPI === "cli") {
            $handler->handleUnconditionally(true);
            return $whoops->handleException($exception);
        }
        $whoops->handleException($exception);
        return null;
    }

    public function isBreak(): bool
    {
        return false;
    }
}