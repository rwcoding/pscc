<?php

namespace Rwcoding\Pscc\Lang;

class Lang
{
    private static array $data;

    public static function setLang(string $language = 'zh-CN'): void
    {
        self::$data = require __DIR__ . '/' .$language.'.php';
    }

    public static function t(string $k, ...$params): string
    {
        $text = self::$data[$k] ?? $k;
        if ($params) {
            foreach ($params as $k=>$v) {
                $text = str_replace('{'.$k.'}', "$v", $text);
            }
        }
        return $text;
    }
}