<?php

namespace Rwcoding\Pscc;

use Rwcoding\Pscc\Lang\Lang;
use Rwcoding\Pscc\Util\ObjectUtil;

/**
 * PSR-11的简单实现
 */
class Container
{
    /**
     * 对象集合
     */
    protected array $objects = [];

    /**
     * 对象定义集合
     */
    protected array $definitions = [];

    public function __get(string $id)
    {
        return $this->get($id);
    }

    public function has($id): bool
    {
        return isset($this->definitions[$id]) || isset($this->objects[$id]);
    }

    public function hasDefinitionAndObject(string $name) : bool
    {
        return isset($this->definitions[$name]) && isset($this->objects[$name]);
    }

    public function hasDefinition(string $name) : bool
    {
        return isset($this->definitions[$name]);
    }

    public function hasObject(string $name): bool
    {
        return isset($this->objects[$name]);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getDefinition(string $name)
    {
        return $this->definitions[$name] ?? null;
    }

    public function get(string $id)
    {
        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }
        if (isset($this->definitions[$id])) {
            $definition = $this->definitions[$id];
            if (is_object($definition) && !is_callable($definition)) {
                $this->objects[$id] = $definition;
            } else {
                $this->objects[$id] = ObjectUtil::create($definition);
            }
        }
        return $this->objects[$id] ?? null;
    }

    public function set(string $name, $definition): void
    {
        unset($this->objects[$name]);

        if (is_object($definition) && !is_callable($definition)) {
            $this->objects[$name] = $definition;
        } else {
            $this->definitions[$name] = $definition;
        }
    }

    public function setMultiple(array $definitions): void
    {
        foreach ($definitions as $name => $definition) {
            self::set($name,  $definition);
        }
    }

    public function delete(string $name): void
    {
        unset($this->definitions[$name], $this->objects[$name]);
    }

    /**
     * 创建一个新的对象
     * @param string $id 配置的对象ID
     * @return mixed
     */
    public function newObject(string $id)
    {
        if (isset($this->definitions[$id])) {
            $definition = $this->definitions[$id];
            if (is_object($definition) && !is_callable($definition)) {
                return $definition;
            } else {
                return ObjectUtil::create($definition);
            }
        }
        throw new \RuntimeException(Lang::t("container-not-found", $id));
    }
}