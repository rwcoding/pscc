<?php
return [
    "locale_path"     => __DIR__."/../resources/lang",
    "locale"          => "zh-CN",
    "locale_fallback" => "en",

    "components" => [
        "config" => [
            "file" => __DIR__."/../pscc.ini"
        ],

        "logger" => [
            "file" => __DIR__."/../../tmp/app.log",
            "dateTimezone" => "PRC",
        ],

        "router" => [
            "_boot" => function($my){
                $my->add([
                    "favicon.ico" => ["\Rwcoding\Examples\Pscc\Apis\Help", "ico"],
                    "__default__" => ["\Rwcoding\Examples\Pscc\Apis\Help", "index"],
                    "test" => ["\Rwcoding\Examples\Pscc\Apis\Help", "test"],

                    "user.list"    => "\Rwcoding\Examples\Pscc\Apis\User\Lists",
                    "user.add"     => "\Rwcoding\Examples\Pscc\Apis\User\Add",
                    "user.edit"    => "\Rwcoding\Examples\Pscc\Apis\User\Edit",
                    "user.del"     => "\Rwcoding\Examples\Pscc\Apis\User\Del",
                    "user.restore" => "\Rwcoding\Examples\Pscc\Apis\User\Restore",

                    "blog.list" => "\Rwcoding\Examples\Pscc\Apis\Blog\Lists",
                    "blog.add"  => "\Rwcoding\Examples\Pscc\Apis\Blog\Add",
                    "blog.edit" => "\Rwcoding\Examples\Pscc\Apis\Blog\Edit",
                    "blog.del"  => "\Rwcoding\Examples\Pscc\Apis\Blog\Del",
                ]);
            }
        ],
    ],
];