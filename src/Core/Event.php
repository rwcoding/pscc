<?php

namespace Rwcoding\Pscc\Core;

use Rwcoding\Pscc\Lang\Lang;

class Event
{
    private array $provider = [];

    public function emit($event, array $listener)
    {
        $id = $this->getEventId($event);
        if (!isset($this->provider[$id])) {
            $this->provider[$id] = [$listener];
        } else {
            $this->provider[$id][] = $listener;
        }
    }

    public function trigger($event)
    {
        $id = $this->getEventId($event);
        if (!isset($this->provider[$id])) {
            return;
        }
        foreach ($this->provider[$id] as $item) {
            if (isset($item[2])) {
                call_user_func_array($item, $item[2]);
            } else {
                call_user_func($item);
            }
        }
    }

    public function getEventId($event): string
    {
        if (is_string($event)){
            return $event;
        } else if ($event instanceof EventInterface) {
            return $event->getEventId();
        } else if(is_object($event)) {
            return spl_object_hash($event);
        }
        throw new \RuntimeException(Lang::t("event-no-id"));
    }

    public function triggerAll()
    {
        foreach ($this->provider as $item) {
            foreach ($item as $val) {
                if (isset($val[2])) {
                    call_user_func_array($val, $val[2]);
                } else {
                    call_user_func($val);
                }
            }
        }
    }
}