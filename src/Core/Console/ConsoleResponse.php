<?php

namespace Rwcoding\Pscc\Core\Console;

class ConsoleResponse
{
    private $body = null;
    private $render = null;

    public function setRender($render): self
    {
        $this->render = $render;
        return $this;
    }

    public function setBody($body): void
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function send()
    {
        if ($this->render) {
            call_user_func($this->render, $this->body);
            return;
        }
        if (is_array($this->body)) {
            echo json_encode($this->body, JSON_UNESCAPED_UNICODE);
        } else {
            echo $this->body;
        }
    }
}