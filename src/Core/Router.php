<?php

namespace Rwcoding\Pscc\Core;

class Router
{
    private array $routes = [];

    public function __construct(array $routes = [])
    {
        $this->routes = $routes;
    }

    /**
     * @param string|array $path
     * @param array|\Closure $obj
     * @return $this
     */
    public function add($path, $obj = null): self
    {
        if (is_array($path)) {
            $this->routes = array_merge($this->routes, $path);
        } else {
            $this->routes[$path] = $obj;
        }
        return $this;
    }

    public function setDefaultRoute($obj): self
    {
        $this->routes["__default__"] = $obj;
        return $this;
    }

    public function getDefaultRoute()
    {
        return $this->routes['__default__'] ?? null;
    }

    public function remove(string $path): self
    {
        if (isset($this->routes[$path])) {
            unset($this->routes[$path]);
        }
        return $this;
    }

    /**
     * @param string $path
     * @return string|null|array|\Closure
     */
    public function find(string $path)
    {
        if (isset($this->routes[$path])) {
            return $this->routes[$path];
        }
        return $this->getDefaultRoute();
    }
}