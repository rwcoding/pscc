<?php

namespace Rwcoding\Pscc\Core\Web\Session;

class Session
{
    private string $sessionId = '';

    private string $idPrefix = '';

    private int $timeout = 86400;

    private array $data = [];

    private bool $isInit = false;

    private bool $reCreateId = false;

    private string $flashParam = '__flash';

    private ?SessionHandlerInterface $handler = null;

    private ?SessionSenderInterface $sender = null;

    /**
     * gc 百分比 分子
     * @var int
     */
    private int $gcProbability = 1;

    /**
     * gc 百分比 分母
     * @var int
     */
    private int $gcDivisor = 100;

    public function __construct(?string $sessionId = '', string $idPrefix = '')
    {
        if ($sessionId) {
            $this->sessionId = $sessionId;
        }
        $this->idPrefix = $idPrefix;
    }

    public function initSession(bool $forGet = true)
    {
        if (!$this->isInit) {
            $data = $this->handler->data();
            if ($forGet && !$data) {
                $this->reCreateId = true;
            }
            $this->data = $data;
        }
    }

    public function createSessionId()
    {
        $time = time();
        $this->sessionId = session_create_id($this->idPrefix);
        if ($this->sender instanceof SessionSenderInterface) {
            $this->sender->send($this->sessionId, $time + $this->timeout);
        } else if (is_callable($this->sender)) {
            call_user_func($this->sender, $this->sessionId, $time + $this->timeout);
        }
        $this->data['create'] = $time;
        $this->data['update'] = $time;
        $this->data['expire'] = $time + $this->timeout;
        $this->data['data'] = [];
        $this->reCreateId = false;
        $this->isInit = true;
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setFlashParamName(string $name): self
    {
        $this->flashParam = $name;
        return $this;
    }

    public function setHandler(SessionHandlerInterface $handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @param $sender SessionSenderInterface|\Closure
     * @return $this
     */
    public function setSender($sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function setGc(int $gcDivisor, int $gcProbability): self
    {
        $this->gcDivisor = $gcDivisor;
        $this->gcProbability = $gcProbability;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function get(string $name)
    {
        if (!$this->sessionId) {
            return null;
        }
        $this->initSession(true);
        return $this->data['data'][$name] ?? null;
    }

    public function set($name, $value = null, bool $isFlash = false)
    {
        if (!$this->sessionId || ($this->reCreateId && !$isFlash)) {
            $this->createSessionId();
        }
        $oldInit = $this->isInit;
        $this->initSession(false);
        if (!$oldInit && !$this->data) {
            $this->createSessionId();
        }
        if (is_array($name)) {
            $this->data['data'] = array_merge($this->data['data'], $name);
        } else {
            $this->data['data'][$name] = $value;
        }
        $this->store();
    }

    public function remove(string $name)
    {
        if ($this->data && isset($this->data['data'][$name])) {
            unset($this->data['data'][$name]);
            $this->store();
        }
    }

    public function destroy()
    {
        $this->handler->destroy();
    }

    public function gc()
    {
        $this->handler->gc();
    }

    private function store()
    {
        $this->handler->store();
    }

    public function setFlash(string $name, $value)
    {
        $data = $this->get($this->flashParam);
        if (!$data) {
            $data = [];
        }
        $data[$name] = $value;
        $this->set($this->flashParam, $data, true);
    }

    public function getFlash(string $name, $defaultValue = null)
    {
        $data = $this->get($this->flashParam);
        if ($data && isset($data[$name])) {
            $this->removeFlash($name);
            return $data[$name];
        }
        return $defaultValue;
    }

    public function removeFlash($name)
    {
        $data = $this->get($this->flashParam);
        if ($data && isset($data[$name])) {
            unset($data[$name]);
            $this->set($this->flashParam, $data, true);
        }
    }

    public function getAllFlash()
    {
        return $this->get($this->flashParam);
    }

    public function removeAllFlash()
    {
        $this->remove($this->flashParam);
    }

    public function __destruct()
    {
        if (mt_rand(1, $this->gcDivisor) <= $this->gcProbability) {
            $this->gc();
        }
    }
}
