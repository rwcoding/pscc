<?php

namespace Rwcoding\Pscc\Util;

class ObjectUtil
{
    public static function create($config)
    {
        if (is_array($config)) {
            return self::createByArray($config);
        }
        if (is_string($config)) {
            return self::createByClassName($config);
        }
        if (is_callable($config)) {
            return self::createByCallable($config);
        }
        return null;
    }

    public static function createByClassName(string $className)
    {
        $obj = new $className();
        if (method_exists($obj, 'init')) {
            $obj->init();
        }
        return $obj;
    }

    public static function createByCallable(callable $cb)
    {
        return $cb();
    }

    public static function createByArray(array $config)
    {
        if (!isset($config['__class'])) {
            return null;
        }
        $class = $config['__class'];
        unset($config['__class']);

        $construct = [];
        if (isset($config['__construct'])) {
            $construct = $config['__construct'];
            unset($config['__construct']);
        }

        if ($construct) {
            $obj = new $class(...$construct);
        } else {
            $obj = new $class;
        }

        $boot = null;
        foreach ($config as $pro => $value) {
            if ($pro == "_boot") {
                $boot = $value;
                continue;
            }
            if (is_array($value) && isset($value['__class'])) {
                $obj->$pro = self::createByArray($value);
            } else {
                $obj->$pro = $value;
            }
        }

        if ($boot) {
            $boot($obj);
        }

        if (method_exists($obj, 'init')) {
            $obj->init();
        }

        return $obj;
    }
}
