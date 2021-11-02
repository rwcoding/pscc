<?php
return [
    "locale_path"     => __DIR__."/../resources/lang",
    "locale"          => "zh-CN",
    "locale_fallback" => "en",

    "components" => [
        "config" => [
            "__construct" => [ __DIR__."/../pscc.ini" ]
        ],

        "app" => [
            "__construct" => [[
                "app" => "My Blog"
            ]]
        ],

        "router" => [
            "__construct" => [[
                "help"      => ["\Rwcoding\Examples\Pscc\Apis\Help", "index"],
                "user.list" => ["\Rwcoding\Examples\Pscc\Apis\User", "index"],
                "user.add"  => ["\Rwcoding\Examples\Pscc\Apis\User", "add"],
                "user.edit" => ["\Rwcoding\Examples\Pscc\Apis\User", "edit"],
                "user.del"  => ["\Rwcoding\Examples\Pscc\Apis\User", "del"],
            ]]
        ],
    ],
];