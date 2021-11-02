<?php

namespace Rwcoding\Pscc\Netio;

abstract class Http extends Server
{
    public function type(): int
    {
        return SERVER_TYPE_HTTP;
    }
}