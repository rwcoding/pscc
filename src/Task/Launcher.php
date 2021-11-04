<?php

namespace Rwcoding\Pscc\Task;

use Rwcoding\Pscc\Core\Context;
use Rwcoding\Pscc\Exception\WhoopsPlainTextHandler;
use Rwcoding\Pscc\Di;
use Swoole\Process\Manager;
use Swoole\Process\Pool;
use Swoole\Table;
use Swoole\Timer;
use Swoole\Event;

/**
 * [ [ "tick"=>10, "route"=>"user.add", "count"=>-1, "start"=>"5:00", "end"=>"7:00" ]]
 */
class Launcher
{
    /**
     * @var array|string
     */
    private $tasks;

    private string $configFile = "";
    private int $workerNum = 1;

    private ?Table $table = null;

    public function __construct($tasks, int $workerNum = 1, string $configFile = "")
    {
        if (is_string($tasks) && !file_exists($tasks)) {
            echo "warning:failure to find task config file \n";
        }

        if ($configFile && !file_exists($configFile)) {
            echo "warning:failure to find application config file \n";
        }

        $this->workerNum = $workerNum;
        $this->configFile = $configFile;
        $this->tasks = $tasks;

        $this->table = new Table(1024);
        $this->table->column('count', Table::TYPE_INT);
        $this->table->column('count_day', Table::TYPE_STRING, 100);
        $this->table->create();
    }

    public function run()
    {
        $pm = new Manager();

        for ($i = 0; $i < $this->workerNum; $i++) {
            $pm->add(function (Pool $pool, int $workerId) {
                $di = Di::my();
                $di->exception->addHandler(new WhoopsPlainTextHandler());
                if ($this->configFile) {
                    $di->init(require $this->configFile);
                }

                if (is_string($this->tasks)) {
                    $tasks = require $this->tasks;
                } else {
                    $tasks = $this->tasks;
                }
                $ticks = [];
                foreach ($tasks as $item) {
                    if (empty($item['tick']) || $item['tick'] < 0) {
                        echo "warning: task need tick \n";
                        continue;
                    }
                    if (empty($item['route'])) {
                        echo "warning: task need route \n";
                        continue;
                    }

                    if (!isset($ticks[$item["tick"]])) {
                        $ticks[$item["tick"]] = [];
                    }
                    $ticks[$item["tick"]][] = $item;
                }

                foreach ($ticks as $timer => $item) {
                    Timer::tick((int)($timer * 1000), function () use($item) {
                        $this->runTask($item);
                    });
                }

                Event::wait();
            });
        }

        $pm->start();
    }

    public function runTask(array $tasks)
    {
        $di = Di::my();
        foreach ($tasks as $task) {
            try {
                $request = new TaskRequest($task);
                if (!$request->canRun()) {
                    continue;
                }

                $route = $request->route;
                $count = (int)$this->table->get($route, "count");
                if ($count >= $request->count && $request->count >= 0) {
                    continue;
                }

                $ymd = date("Ymd");
                $countDay = 1;
                if ($day = $this->table->get($route, "count_day")) {
                    $d = json_decode($day, true);
                    if (!empty($d[$ymd])) {
                        if ($d[$ymd] >= $request->dayCount && $request->dayCount >= 0) {
                            continue;
                        }
                        $countDay = $d[$ymd] + 1;
                    }
                }
                $this->table->set($route, ["count_day" => json_encode([$ymd => $countDay]), "count"=>$count+1]);

                $di->app->run(new Context($request, new TaskResponse()));
            } catch (\Throwable $e) {
                $di->exception->handle($e);
                foreach ($di->exception->getResult() as $item) {
                    echo $item;
                }
            }
        }
    }
}