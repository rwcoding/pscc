<?php

namespace Rwcoding\Pscc\Util;


/**
 * 解析命令行参数
 */
class ConsoleUtil
{
    private string $script = "";
    private array $flags = [];
    private array $commands = [];

    public function __construct()
    {
        list($this->script, $this->flags, $this->commands) = $this->parse($_SERVER['argv']);
    }

    public function parse(array $argv): array
    {
        $script = "";
        $flags = [];
        $commands = [];
        $length = count($argv);
        for ($i = 0; $i < $length; $i++) {
            if ($i == 0) {
                $script = $argv[$i];
                continue;
            }
            if (strpos($argv[$i], "=")) {
                $arr = explode("=", $argv[$i]);
                if (strncmp($arr[0], "--", 2) == 0) {
                    $flags[substr($arr[0],2)] = $arr[1];
                } else if (strncmp($arr[0], "-", 1) == 0) {
                    $flags[substr($arr[0],1)] = $arr[1];
                } else {
                    $flags[$arr[0]] = $arr[1];
                }
                continue;
            }

            if (strncmp($argv[$i], "--", 2) == 0) {
                $flags[substr($argv[$i],2)] = true;
            } else if (strncmp($argv[$i], "-", 1) == 0) {
                $flags[substr($argv[$i],1)] = true;
            } else {
                $commands[$argv[$i]] = true;
            }
        }
        return [$script, $flags, $commands];
    }

    public function getScript(): string
    {
        return $this->script;
    }

    public function hasFlag(string $name): bool
    {
        return isset($this->flags[$name]);
    }

    public function getString(string $name): string
    {
        return $this->flags[$name] ?? "";
    }

    public function getFlag(string $name): ?string
    {
        return $this->flags[$name] ?? null;
    }

    public function hasCommand(string $name): bool
    {
        return isset($this->commands[$name]);
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }
}