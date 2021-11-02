<?php

namespace Rwcoding\Pscc\Netio;

abstract class WebSocket extends Server
{
    public function type(): int
    {
        return SERVER_TYPE_WEBSOCKET;
    }
}