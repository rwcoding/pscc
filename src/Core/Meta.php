<?php

namespace Rwcoding\Pscc\Core;

class Meta
{
    private array $__data;

    public function __construct(array $data = [])
    {
        $this->__data = $data;
    }

    public function __get(string $name)
    {
        $method = 'get'.$name;
        if (method_exists($this,$method)) {
            return $this->$method();
        }
        return $this->__data[$name] ?? null;
    }

    public function __set(string $name, $value)
    {
        $method = 'set'.$name;
        if (method_exists($this,$method)) {
            $this->$method();
        } else {
            $this->__data[$name] = $value;
        }
    }

    protected function getMetaData(): array
    {
        return $this->__data;
    }

    public function get(string $name)
    {
        return $this->__data[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->__data[$name]);
    }

    public function set(string $name, $value)
    {
        $this->__data[$name] = $value;
    }
}