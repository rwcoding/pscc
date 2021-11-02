<?php

namespace Rwcoding\Pscc\Core\Console;

use Rwcoding\Pscc\Core\PathFinderInterface;
use Rwcoding\Pscc\Util\ConsoleUtil;

class ConsoleRequest extends ConsoleUtil implements PathFinderInterface
{
    public function getPathForRoute(): string
    {
        foreach ($this->getCommands() as $key=>$val) {
            return $key;
        }
        return "";
    }

    public function getPathInfo(): string
    {
        return $this->getPathForRoute();
    }
}