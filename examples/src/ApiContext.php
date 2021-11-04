<?php

namespace Rwcoding\Examples\Pscc;

use Rwcoding\Pscc\Core\Context;
use Rwcoding\Pscc\Di;

/**
 * @property array $data
 * @property string $command
 */
class ApiContext extends Context
{
    private string $command = "";
    private array $data = [];

    public function init()
    {
        if (PHP_SAPI == "cli" && !defined("PSCC_IN_SERVER")) {
            $this->command = $this->request->getPathInfo() ?: "help";
            $this->data = $this->request->getFlags();
        } else {
            if ($this->request->isGet()) {
                $this->command = substr($this->request->getPathInfo(),1) ?: "help";
                $this->data = $this->request->getQueries();
            }
            if ($this->request->isPost()) {
                $this->data = json_decode($this->request->getBody(), true);
                $this->command = $this->data["cmd"] ?? "help";
            }
        }
    }

    public function getPathForRoute(): string
    {
        return $this->command;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function emitResponse()
    {
        if (Di::inWeb()) {
            if (!$this->response->hasHeader('Content-Type')) {
                $this->response->addHeader("Content-Type", "application/json");
            }
            $this->response->send();
        } else {
            $this->response->setRender(function ($body) {
                if (is_string($body)) {
                    return $body;
                }
                $text  = str_repeat("·",100)."\n";
                $text .= "[command] ".$this->command."\n";
                $text .= "   [data] ".json_encode($this->data, JSON_UNESCAPED_UNICODE)."\n";
                $text .= " [result] ".json_encode($body, JSON_UNESCAPED_UNICODE)."\n";
                $text .= str_repeat("·",100)."\n";
                return $text;
            })->send();
        }
    }
}