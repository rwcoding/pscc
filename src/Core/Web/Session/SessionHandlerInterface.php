<?php

namespace Rwcoding\Pscc\Core\Web\Session;

interface SessionHandlerInterface
{
    public function data(): array;
    public function gc(): void;
    public function store(): bool;
    public function destroy(): void;
}
