<?php
namespace Rwcoding\Pscc;

use Rwcoding\Pscc\Core\Config;
use Rwcoding\Pscc\Util\ConsoleUtil;
use Rwcoding\Pscc\Netio\EventInterface;

/**
 * @property Config $conf
 * @property ConsoleUtil $console
 * @property \Swoole\Server|\Swoole\Http\Server|\Swoole\WebSocket\Server $server
 */
class Bootstrap
{
    private Config $conf;
    private ConsoleUtil $console;
    private $server;

    public function __construct()
    {
        $this->console = new ConsoleUtil();
        $this->conf = new Config($this->console->getString("conf"));
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function run()
    {
        $this->server->start();
    }

    public function register(EventInterface $event)
    {
        define("PSCC_IN_SWOOLE", true);
        define("PSCC_IN_SERVER", true);
        $setting = $this->conf->getSwoole();
        $host = !empty($setting['host']) ? $setting['host'] : "127.0.0.1";
        $port = !empty($setting['port']) ? (int)$setting['port'] : 8080;

        if (isset($setting['host'])) {
            unset($setting['host']);
        }

        if (isset($setting['port'])) {
            unset($setting['port']);
        }

        if ($event->isHttp()) {
            define("PSCC_IN_HTTP", true);
            $server = new \Swoole\Http\Server($host, $port);
        } else if ($event->isWebSocket()) {
            define("PSCC_IN_WEBSOCKET", true);
            $server = new \Swoole\WebSocket\Server($host, $port);
        } else if($event->isTcp()) {
            define("PSCC_IN_TCP", true);
            $server = new \Swoole\Server($host, $port);
        } else {
            define("PSCC_IN_UDP", true);
            $server = new \Swoole\Server($host, $port, SWOOLE_PROCESS, SWOOLE_UDP);
        }
        $server->set($setting);
        $server->on("start", [$event, "onStart"]);
        $server->on("shutdown", [$event, "onShutdown"]);
        $server->on("workerStart", [$event, "onWorkerStart"]);
        $server->on("workerStop", [$event, "onWorkerStop"]);
        $server->on("workerExit", [$event, "onWorkerExit"]);
        $server->on("workerError", [$event, "onWorkerError"]);
        $server->on("managerStart", [$event, "onManagerStart"]);
        $server->on("managerStop", [$event, "onManagerStop"]);
        $server->on("beforeReload", [$event, "onBeforeReload"]);
        $server->on("afterReload", [$event, "onAfterReload"]);
        $server->on("pipeMessage", [$event, "onPipeMessage"]);

        if ($server->setting['task_worker_num'] > 0) {
            $server->on("task", [$event, "onTask"]);
            $server->on("finish", [$event, "onFinish"]);
        }

        if ($event->isTcp()) {
            $server->on("close", [$event, "onClose"]);
            $server->on("connect", [$event, "onConnect"]);
            $server->on("receive", [$event, "onReceive"]);
        }

        if ($event->isUdp()) {
            $server->on("packet", [$event, "onPacket"]);
        }

        if ($event->isHttp()) {
            $server->on("request", [$event, "onRequest"]);
        }

        if ($event->isWebSocket()) {
            // $server->on("handShake", [$event, "onHandShake"]);
            $server->on("open", [$event, "onOpen"]);
            $server->on("message", [$event, "onMessage"]);
            $server->on("disconnect", [$event, "onDisconnect"]);
        }

        return $this->server = $server;
    }

    public function getServer()
    {
        return $this->server;
    }
}