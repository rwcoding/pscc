<?php

namespace Rwcoding\Pscc\Core\Web;

use Rwcoding\Pscc\Lang\Lang;
use RuntimeException;

trait ResponseTrait
{
    protected array $headers = [];

    /**
     * @var string|object
     */
    protected $body;

    protected array $cookies = [];

    protected int $status = 0;

    private ?\Closure $render = null;

    public function setRender($render): self
    {
        $this->render = $render;
        return $this;
    }

    public function addHeader(string $name, $data): self
    {
        $this->headers[$name] = $data;
        return $this;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    public function removeHeader(string $name): self
    {
        if (isset($this->headers[$name])) {
            unset($this->headers[$name]);
        }
        return $this;
    }

    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function addCookie(string $name, array $data): self
    {
        $this->cookies[$name] = $data;
        return $this;
    }

    public function removeCookie(string $name): self
    {
        if (isset($this->cookies[$name])) {
            unset($this->cookies[$name]);
        }
        return $this;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }
}