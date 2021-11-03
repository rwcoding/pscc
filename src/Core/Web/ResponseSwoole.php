<?php

namespace Rwcoding\Pscc\Core\Web;

use Rwcoding\Pscc\Core\ResponseInterface;

class ResponseSwoole implements ResponseInterface
{
    use ResponseTrait;

    private \Swoole\Http\Response $swooleResponse;

    public function __construct(\Swoole\Http\Response $swooleResponse)
    {
        $this->swooleResponse = $swooleResponse;
    }

    public function send(): void
    {
        if ($this->status) {
            $this->swooleResponse->setStatusCode($this->status);
        }
        if ($this->cookies) {
            foreach ($this->cookies as $name => $cookie) {
                $this->swooleResponse->cookie(
                    $name,
                    $cookie['value'],
                    $cookie['expire'] ?? 0,
                    $cookie['path'] ?? '/',
                    $cookie['domain'] ?? '',
                    $cookie['secure'] ?? false,
                    $cookie['httponly'] ?? true,
                    $cookie['samesite'] ?? '',
                );
            }
        }
        if ($this->headers) {
            foreach ($this->headers as $k => $v) {
                $this->swooleResponse->header(ucfirst($k), $v);
            }
        }

        if ($this->render) {
            $ret = call_user_func($this->render, $this->body);
        } else {
            if (is_array($this->body)) {
                $ret = json_encode($this->body, JSON_UNESCAPED_UNICODE);
            } else if (is_object($this->body)) {
                $ret = $this->body->__toString();
            } else {
                $ret = $this->body;
            }
        }

        $this->swooleResponse->end($ret);
    }

    public function redirect(string $url, $code = 302): self
    {
        $this->swooleResponse->redirect($url, $code);
        return $this;
    }
}