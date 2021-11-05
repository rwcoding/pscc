<?php

use Rwcoding\Pscc\Util\ConsoleUtil;

require __DIR__."/../vendor/autoload.php";

$console = new ConsoleUtil();

if ($console->hasCommand("start")) {
    require __DIR__."/swoole.php";
    return;
}

if ($console->hasCommand("task")) {
    require __DIR__."/task.php";
    return;
}

require __DIR__."/cli.php";