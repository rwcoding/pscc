<?php

namespace Rwcoding\Pscc\Core\Web\Session;

interface SessionSenderInterface
{
    /**
     * 发送session到客户端，通常为cookie
     * @param string $id  session id
     * @param int $expire 过期时间，unix时间戳
     */
    public function send(string $id, int $expire): void;
}