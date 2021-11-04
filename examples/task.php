<?php
require __DIR__."/../vendor/autoload.php";

use Rwcoding\Pscc\Task\Launcher;

(new Launcher([
    ["tick"=>2, "route"=>"help"]
], 1, __DIR__.'/config/main.php'))->run();