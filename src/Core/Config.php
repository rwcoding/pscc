<?php

namespace Rwcoding\Pscc\Core;

use Error;

class Config
{
    private array $config = [];

    /**
     * @param array|string $file
     * @throws Error
     */
    public function __construct($file)
    {
        if (is_array($file)) {
            $this->config = $file;
            return;
        }
        if (!$file) {
            $file = getcwd()."/pscc.ini";
            if (!file_exists($file)) {
                $file = "/etc/pscc.ini";
            }
        }
        $f = realpath($file);
        if (!$f) {
            return;
        }
        if ($ret = parse_ini_file($f, true)) {
            $this->config = $ret;
        }
    }

    public function gets(): array
    {
        return $this->config;
    }

    public function get(string $key)
    {
        return $this->config[$key] ?? null;
    }

    public function getSwoole(): array
    {
        $swoole = $this->config['swoole'];
        if (!isset($swoole['enable_coroutine'])) {
            $swoole['enable_coroutine'] = false;
        }
        if (empty($swoole['worker_num'])) {
            $swoole['worker_num'] = swoole_cpu_num() * 4;
        } else {
            if (substr($swoole['worker_num'], 0, 1) === "*") {
                $swoole['worker_num'] = swoole_cpu_num() * (int)substr($swoole['worker_num'], 1);
            }
        }

        if (isset($swoole['task_worker_num']) && substr($swoole['task_worker_num'],0,1) == "*") {
            $swoole['task_worker_num'] = swoole_cpu_num() * (int)substr($swoole['task_worker_num'], 1);
        }

        $swoole['task_use_object'] = true;
        $swoole['event_object'] = true;

        return $swoole;
    }
}