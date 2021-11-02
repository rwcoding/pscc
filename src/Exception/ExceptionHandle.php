<?php

namespace Rwcoding\Pscc\Exception;

use Throwable;
use ErrorException;

class ExceptionHandle
{
    /**
     * @var array<ExceptionHandlerInterface> 异常处理集合
     */
    private array $handlers = [];

    private array $handleResult = [];

    public function __construct()
    {
        set_exception_handler(function(Throwable $e) {
           $this->handle($e);
        });
        set_error_handler(function (int $severity, string $message, string $file, int $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });
        register_shutdown_function(function () {
            if ($error = error_get_last()) {
                throw new ErrorException($error['message'], 0, E_ERROR, $error['file'], $error['line']);
            }
        });
    }

    public function addHandler($handler) 
    {
        $this->handlers[] = $handler;
    }
    
    public function cleanHandler()
    {
        $this->handlers = [];
    }

    /**
     * 处理异常
     * @param Throwable $exception
     */
    public function handle(Throwable $exception) 
    {
        $this->handleResult = [];
        if($exception->getCode() === -1) {
            return;
        }
        if (!$this->handlers) {
            (new DefaultHandler())->handle($exception);
        }
        foreach ($this->handlers as $handler) {
            $this->handleResult[] = $handler->handle($exception);
            if ($handler->isBreak()) {
                break;
            }
        }
    }

    public function getResult(bool $isClean = false) : array
    {
        if ($isClean) {
            $ret = $this->handleResult;
            $this->handleResult = [];
            return $ret;
        }
        return $this->handleResult;
    }

    public function cleanResult()
    {
        $this->handleResult = [];
    }

    public function getMessage() : string
    {
        $msg = '';
        foreach ($this->handleResult as $result) {
            if (is_null($result)) {
                continue;
            }
            $msg .= $result;
        }
        return $msg;
    }

    public function reset(): self
    {
        $this->handlers = [];
        $this->handleResult = [];
        return $this;
    }
}