<?php

namespace Rwcoding\Pscc\Core\Web;

use Rwcoding\Pscc\Core\PathFinderInterface;
use function Swoole\Coroutine\Http\request;

class Request implements PathFinderInterface
{
    use RequestTrait;

    private ?array $headers = null;
    private ?string $body = null;

    public function getQueries(): array
    {
        return $_GET;
    }

    public function getPosts(): array
    {
        return $_POST;
    }

    public function getHeaders(): array
    {
        if ($this->headers !== null) {
            return $this->headers;
        }
        if (function_exists('getallheaders')) {
            $this->headers = getallheaders();
        } else {
            foreach ($_SERVER as $name => $value) {
                if (strncmp($name, 'HTTP_', 5) === 0) {
                    $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $this->headers[$name] = $value;
                }
            }
        }
        return $this->headers;
    }

    public function getFiles(): array
    {
        return $_FILES;
    }

    public function getCookies(): array
    {
        return $_COOKIE;
    }

    public function getBody(): string
    {
        if ($this->body === null) {
            $this->body = file_get_contents("php://input");
        }
        return $this->body;
    }

    public function getMethod(): string
    {
        $method = "GET";
        if (isset($this->getHeaders()['X-Http-Method-Override'])) {
            $method = strtoupper($this->getHeaders()['X-Http-Method-Override']);
        } elseif (isset($_SERVER['REQUEST_METHOD'])) {
            $method = strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return $method;
    }

    public function getPathForRoute(): string
    {
        return $this->getPathInfo();
    }

    public function getPathInfo(): string
    {
        if (isset($_SERVER['PATH_INFO'])) {
            return $_SERVER['PATH_INFO'];
        }
        $requestUri = $_SERVER['REQUEST_URI'];
        $pos = strpos($requestUri, '?');
        if (strpos($requestUri, '?') !== false) {
            return substr($requestUri, 0, $pos);
        }
        return $requestUri;
    }

    public function getFullUrl(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
            "://" .
            $_SERVER['HTTP_HOST'] .
            $_SERVER['REQUEST_URI'];
    }

    public function getReferrer(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? '/';
    }

    public function isHttps(): bool
    {
        return strtoupper($_SERVER['REQUEST_SCHEME']) === 'HTTPS';
    }
}