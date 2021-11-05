<?php

namespace Rwcoding\Pscc\Task;

use Rwcoding\Pscc\Core\PathFinderInterface;
use Rwcoding\Pscc\Di;
use Swoole\Table;

class TaskRequest implements PathFinderInterface
{
    public string $route;

    public ?\Closure $workerScope = null;

    public string $scope = '';
    public string $dayScope = '';

    public int $count = -1;
    public int $dayCount = -1;

    public function __construct(array $task)
    {
        $this->route = $task['route'];
        $this->workerScope = $task['workers'] ?? null;
        $this->scope = $task['scope'] ?? '';
        $this->dayScope = $task['day_scope'] ?? '';
        $this->count = intval($task['count'] ?? -1);
        $this->dayCount = intval($task['day_count'] ?? -1);
    }

    public function getPathForRoute(): string
    {
        return $this->route;
    }

    public function canRun(Table $table): bool
    {
        $time = time();
        $sp = "~";
        if ($arr = explode("|",$this->scope)) {
            $oks = false;
            foreach ($arr as $val) {
                $ok = true;
                $tmp = explode($sp, $val);
                if (!empty($tmp[0]) && $time < (int)strtotime($tmp[0])) {
                    $ok = false;
                }
                if (!empty($tmp[1]) && $time > (int)strtotime($tmp[1])) {
                    $ok = false;
                }
                if ($ok) {
                    $oks = true;
                    break;
                }
            }
            if (!$oks) {
                return false;
            }
        }

        $ct = (int)date("Hi", $time);
        if ($arr = explode("|",$this->dayScope)) {
            $oks = false;
            foreach ($arr as $val) {
                $ok = true;
                $tmp = explode($sp, $val);
                if (!empty($tmp[0]) && $ct < (int)str_replace($tmp[0], ":", "")) {
                    $ok = false;
                }
                if (!empty($tmp[1]) && $ct > (int)str_replace($tmp[1], ":", "")) {
                    $ok = false;
                }
                if ($ok) {
                    $oks = true;
                    break;
                }
            }
            if (!$oks) {
                return false;
            }
        }

        if ($this->workerScope) {
            if(! ($this->workerScope)(Di::my()->app->params['worker_id'])) {
                return false;
            }
        }

        if ($this->scope || $this->dayScope) {
            $route = $this->route;
            $count = (int)$table->get($route, "count");
            if ($count >= $this->count && $this->count >= 0) {
                return false;
            }

            $ymd = date("Ymd");
            $countDay = 1;
            if ($day = $table->get($route, "count_day")) {
                $d = json_decode($day, true);
                if (!empty($d[$ymd])) {
                    if ($d[$ymd] >= $this->dayCount && $this->dayCount >= 0) {
                        return false;
                    }
                    $countDay = $d[$ymd] + 1;
                }
            }
            $table->set($route, ["count_day" => json_encode([$ymd => $countDay]), "count"=>$count+1]);
        }

        return true;
    }
}