<?php

namespace Rwcoding\Pscc\Task;

use Rwcoding\Pscc\Core\PathFinderInterface;

class TaskRequest implements PathFinderInterface
{
    public string $route;

    public int $start = 0;
    public int $end   = 0;

    public int $dayStart = 0;
    public int $dayEnd = 0;

    public int $count = -1;
    public int $dayCount = -1;

    public function __construct(array $task)
    {
        $this->route = $task['route'];
        $this->start = intval($task['start'] ?? 0);
        $this->end = intval($task['end'] ?? 0);
        $this->count = intval($task['count'] ?? -1);
        $this->dayStart = intval($task['day_start'] ?? 0);
        $this->dayEnd = intval($task['day_end'] ?? 0);
        $this->dayCount = intval($task['day_count'] ?? -1);
    }

    public function getPathForRoute(): string
    {
        return $this->route;
    }

    public function canRun(): bool
    {
        $time = time();
        if (($time > $this->end && $this->end > 0) || $time < $this->start) {
            return false;
        }

        $ct = (int)date("Hi", $time);
        if (($ct > $this->dayEnd && $this->dayEnd > 0) || $ct < $this->dayStart) {
            return false;
        }

        return true;
    }
}