<?php

namespace Rwcoding\Pscc\Core\Web;

trait RequestTrait
{
    private ?Uri $uri = null;

    public function getUri(): Uri
    {
        if (!$this->uri) {
            $this->uri = new Uri($this->getFullUrl());
        }
        return $this->uri;
    }

    public function getQuery(string $name, $defaultValue = null)
    {
        return $this->getQueries()[$name] ?? $defaultValue;
    }

    public function getPost(string $name, $defaultValue = null)
    {
        return $this->getPosts()[$name] ?? $defaultValue;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->getHeaders()[$name]);
    }

    public function getHeader(string $name): ?string
    {
        return $this->getHeaders()[$name] ?? null;
    }

    public function getFile(string $name): ?array
    {
        return $this->getFiles()[$name] ?? null;
    }

    public function getCookie(string $name): ?string
    {
        return $this->getCookies()[$name] ?? null;
    }

    public function isAjaxRequest(): bool
    {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isHead(): bool
    {
        return $this->getMethod() === 'HEAD';
    }

    public function isOptions(): bool
    {
        return $this->getMethod() === 'OPTIONS';
    }

    public function isDelete(): bool
    {
        return $this->getMethod() === 'DELETE';
    }

    public function isPut(): bool
    {
        return $this->getMethod() === 'PUT';
    }

    public function isPatch(): bool
    {
        return $this->getMethod() === 'PATCH';
    }
}