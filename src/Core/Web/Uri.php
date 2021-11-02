<?php

namespace Rwcoding\Pscc\Core\Web;

class Uri
{
    private string $scheme = '';

    private string $user = '';

    private string $pass = '';

    private string $host = '';

    private int $port = 80;

    private string $path = '';

    private string $query = '';

    private string $fragment = '';

    public function __construct(string $uri = '')
    {
        if ($uri) {
            $this->parseUri($uri);
        }
    }

    private function parseUri(string $uri): void
    {
        $parts = parse_url($uri);

        if (false === $parts) {
            return;
        }

        $this->scheme   = $parts['scheme'] ?? '';
        $this->user     = $parts['user'] ?? '';
        $this->pass     = $parts['pass'] ?? '';
        $this->host     = $parts['host'] ?? '';
        $this->port     = $parts['port'] ?? 80;
        $this->path     = $parts['path'] ?? '';
        $this->query    = $parts['query'] ?? '';
        $this->fragment = $parts['fragment'] ?? '';
    }

    public function scheme(): string
    {
        return $this->scheme;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function pass(): string
    {
        return $this->pass;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function port(): int
    {
        return $this->port;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(): string
    {
        return $this->query;
    }

    public function fragment(): string
    {
        return $this->fragment;
    }

    public function authority(): string
    {
        $authority = '';
        if ($this->user) {
            $authority .= $this->user;
        }
        if ($this->pass) {
            $authority .= ':' . $this->pass;
        }
        $authority .= $this->host;
        if ($this->port != 80) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }

    public function getRequestUri(): string 
    {
        $uri = '';
        if ('' !== $this->path && '/' !== substr($this->path, 0, 1)) {
            $uri .= '/' . $this->path;
        } else {
            $uri .= $this->path;
        }
        if ('' !== $this->query) {
            $uri .= sprintf('?%s', $this->query);
        }
        if ('' !== $this->fragment) {
            $uri .= sprintf('#%s', $this->fragment);
        }
        return $uri;
    }

    public function __toString(): string
    {
        return self::buildString($this->scheme, $this->authority(), $this->path, $this->query, $this->fragment);
    }

    public static function build(
        string $scheme,
        string $authority,
        string $path,
        string $query,
        string $fragment
    ): Uri {
        return new Uri(self::buildString($scheme, $authority, $path, $query, $fragment));
    }

    public static function buildString(
        string $scheme,
        string $authority,
        string $path,
        string $query,
        string $fragment
    ): string {
        $uri = '';

        if ('' !== $scheme) {
            $uri .= sprintf('%s:', $scheme);
        }

        if ('' !== $authority) {
            $uri .= '//' . $authority;
        }

        if ('' !== $path && '/' !== substr($path, 0, 1)) {
            $path = '/' . $path;
        }

        $uri .= $path;


        if ('' !== $query) {
            $uri .= sprintf('?%s', $query);
        }

        if ('' !== $fragment) {
            $uri .= sprintf('#%s', $fragment);
        }

        return $uri;
    }
}
