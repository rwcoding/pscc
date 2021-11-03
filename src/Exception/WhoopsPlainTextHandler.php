<?php

namespace Rwcoding\Pscc\Exception;

class WhoopsPlainTextHandler implements ExceptionHandlerInterface
{
    public function handle(\Throwable $exception): ?string
    {
        $whoops = new \Whoops\Run;
        $whoops->sendHttpCode(404);
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
        return $whoops->handleException($exception);
    }

    public function isBreak(): bool
    {
        return false;
    }
}