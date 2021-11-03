<?php

namespace Rwcoding\Examples\Pscc;

use Illuminate\Support\Facades\DB;
use Rwcoding\Pscc\Di;

class Hook
{
    public static function over()
    {
        if (DB::getFacadeRoot() && Di::inConsole()) {
            foreach (DB::getQueryLog() as $log) {
                echo "[query] ".$log["query"]."\n";
                echo " [time] ".$log["time"]."\n";
                echo " [bind] ".json_encode($log["bindings"], JSON_UNESCAPED_UNICODE)."\n";
                echo str_repeat("-",60)."\n";
            }
        }
    }
}