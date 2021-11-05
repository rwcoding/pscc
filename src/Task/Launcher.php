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
 * [ [ "tick"=>10, "route"=>"user.add", "day_scope"=>"0:00~5:00", "scope"=>"2021-10-10~2021-12-12", "workers"=>function($id){return $id>1;} ]]
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
                $di->app->params['worker_id'] = $workerId;
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

    public function runTask(array $taskList)
    {
        $di = Di::my();
        foreach ($taskList as $task) {
            try {
                $request = new TaskRequest($task);
                if (!$request->canRun($this->table)) {
                    continue;
                }
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