<?php

namespace Rwcoding\Examples\Pscc;

use Rwcoding\Pscc\Core\Context;

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
                $this->command = substr($this->request->getPathInfo(),1);
                $this->data = $this->request->getQueries();
            }
            if ($this->request->isPost()) {
                $this->data = json_decode($this->request->getBody(), true);
                $this->command = $this->data["cmd"] ?? "";
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
        if (PHP_SAPI == "cli") {
            $this->response->setRender(function ($body) {
                echo str_repeat("·",100)."\n";
                echo "[command] ".$this->command."\n";
                echo "   [data] ".json_encode($this->data, JSON_UNESCAPED_UNICODE)."\n";
                echo " [result] ".json_encode($body, JSON_UNESCAPED_UNICODE)."\n";
                echo str_repeat("·",100)."\n";
            })->send();
        } else {
            $this->response->send();
        }
    }
}