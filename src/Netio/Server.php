<?php

namespace Rwcoding\Pscc\Netio;

const SERVER_TYPE_TCP       = 1;
const SERVER_TYPE_UDP       = 2;
const SERVER_TYPE_HTTP      = 3;
const SERVER_TYPE_WEBSOCKET = 4;

abstract class Server
{
    private int $type = 1;

    public function onStart(\Swoole\Server $server)
    {

    }

    public function onBeforeShutdown(\Swoole\Server $server)
    {

    }

    public function onShutdown(\Swoole\Server $server)
    {

    }

    public function onWorkerStart(\Swoole\Server $server, int $workerId)
    {

    }

    public function onWorkerStop(\Swoole\Server $server, int $workerId)
    {

    }

    public function onWorkerExit(\Swoole\Server $server, int $workerId)
    {

    }

    public function onConnect(\Swoole\Server $server, \Swoole\Server\Event $object)
    {

    }

    public function onReceive(\Swoole\Server $server, \Swoole\Server\Event $object)
    {

    }

    public function onPacket(\Swoole\Server $server, \Swoole\Server\Packet $object)
    {

    }

    public function onClose(\Swoole\Server $server, \Swoole\Server\Event $object)
    {

    }

    public function onTask(\Swoole\Server $server, \Swoole\Server\Task $task)
    {

    }

    public function onFinish(\Swoole\Server $server, \Swoole\Server\TaskResult $result)
    {

    }

    public function onPipeMessage(\Swoole\Server $server, \Swoole\Server\PipeMessage $msg)
    {

    }

    public function onWorkerError(\Swoole\Server $server, \Swoole\Server\StatusInfo $info)
    {

    }

    public function onManagerStart(\Swoole\Server $server)
    {

    }

    public function onManagerStop(\Swoole\Server $server)
    {

    }

    public function onBeforeReload(\Swoole\Server $server)
    {

    }

    public function onAfterReload(\Swoole\Server $server)
    {

    }

    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {

    }

    public function onHandShake(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {

    }

    public function onOpen(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request)
    {

    }

    public function onMessage(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame)
    {

    }

    public function onDisconnect(\Swoole\WebSocket\Server $server, int $fd)
    {

    }

    abstract public function type(): int;

    public function isTcp(): bool
    {
        return $this->type() == SERVER_TYPE_TCP;
    }

    public function isUdp(): bool
    {
        return $this->type() == SERVER_TYPE_UDP;
    }

    public function isHttp(): bool
    {
        return $this->type() == SERVER_TYPE_HTTP;
    }

    public function isWebSocket(): bool
    {
        return $this->type() == SERVER_TYPE_WEBSOCKET;
    }
}