<?php

namespace Rwcoding\Pscc\Exception;

interface ExceptionHandlerInterface
{
    public function handle(\Throwable $exception);
    public function isBreak() : bool;
}