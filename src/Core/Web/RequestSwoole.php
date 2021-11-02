<?php

namespace Rwcoding\Pscc\Core\Web;

use Rwcoding\Pscc\Core\PathFinderInterface;

class RequestSwoole implements PathFinderInterface
{
    use RequestTrait;

    private \Swoole\Http\Request $swooleRequest;

    public function __construct(\Swoole\Http\Request $swooleRequest)
    {
        $this->swooleRequest = $swooleRequest;
    }

    public function setSwooleRequest(\Swoole\Http\Request $swooleRequest)
    {
        $this->swooleRequest = $swooleRequest;
    }

    public function getQueries(): array
    {
        return $this->swooleRequest->get ?? [];
    }

    public function getPosts(): array
    {
        return $this->swooleRequest->post ?? [];
    }

    public function getHeaders(): array
    {
        return $this->swooleRequest->header;
    }

    public function getFiles(): array
    {
        return $this->swooleRequest->files ?? [];
    }

    public function getCookies(): array
    {
        return $this->swooleRequest->cookie ?? [];
    }

    public function getBody(): string
    {
        return $this->swooleRequest->rawContent();
    }

    public function getMethod(): string
    {
        return strtoupper($this->swooleRequest->server['request_method']);
    }

    public function getPathInfo(): string
    {
        return $this->swooleRequest->server['path_info'];
    }

    public function getPathForRoute(): string
    {
        return $this->swooleRequest->server['path_info'];
    }

    public function getFullUrl(): string
    {
        $isHttps = strpos($this->swooleRequest->server['server_protocol'],'HTTPS') !== false;
        $url = ($isHttps?'https':'http')."://" .
            $this->swooleRequest->header['host'] .
            $this->swooleRequest->server['request_uri'];
        if (!empty($this->swooleRequest->server['query_string'])) {
            $url .= '?'.$this->swooleRequest->server['query_string'];
        }
        return $url;
    }

    public function getReferrer(): string
    {
        return $this->swooleRequest->header['referer'] ?? '/';
    }

    public function isHttps(): bool
    {
        return strpos($this->swooleRequest->server['server_protocol'],'HTTPS') !== false;
    }
}