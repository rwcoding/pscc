<?php

namespace Rwcoding\Pscc\Core;

use DateTimeZone;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Rwcoding\Pscc\Lang\Lang;

/**
 * @mixin \Monolog\Logger
 */
class Logger
{
    public string $file = "";
    public int    $level = \Monolog\Logger::NOTICE;
    public string $handler = "rotating";
    public string $formatter = "text";
    public string $channel = "app";
    public string $fileNameFormat = "{filename}.{date}";
    public string $fileDateFormat = "Ymd";
    public string $dateFormat = "Y-m-d H:i:s.v";
    public string $dateTimezone = "UTC";
    public string $logFormat = "[%datetime%] %channel%.%level_name%: %message% %context%\n";

    private \Monolog\Logger $logger;

    public function init()
    {
        if (!$this->file) {
            throw new \RuntimeException(Lang::t("logger-param-file"));
        }
        $log = new \Monolog\Logger('app');
        $log->setTimezone(new DateTimeZone($this->dateTimezone));

        if ($this->formatter == "text") {
            $formatter = new LineFormatter($this->logFormat, $this->dateFormat, false, true);
        } else {
            $formatter = new JsonFormatter(JsonFormatter::BATCH_MODE_JSON, true, true);
            $formatter->setDateFormat($this->dateFormat);
        }

        if ($this->handler == "stream") {
            $handler = new StreamHandler($this->file, $this->level);
        } else {
            $handler = new RotatingFileHandler($this->file, $this->level);
            $handler->setFilenameFormat($this->fileNameFormat,$this->fileDateFormat);
        }
        $handler->setFormatter($formatter);
        $log->pushHandler($handler);
        $this->logger = $log;
    }

    public function __call($name, $args)
    {
        call_user_func_array([$this->logger, $name], $args);
    }
}