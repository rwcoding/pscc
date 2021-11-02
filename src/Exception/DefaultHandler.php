<?php

namespace Rwcoding\Pscc\Exception;

class DefaultHandler implements ExceptionHandlerInterface
{
    public function handle(\Throwable $e): string
    {
        return $e->getMessage()."\n";
    }

    public function isBreak(): bool
    {
        return false;
    }
}