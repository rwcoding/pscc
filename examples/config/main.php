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
                "favicon.ico" => ["\Rwcoding\Examples\Pscc\Apis\Help", "ico"],
                "__default__" => ["\Rwcoding\Examples\Pscc\Apis\Help", "index"],

                "user.list"    => "\Rwcoding\Examples\Pscc\Apis\User\Lists",
                "user.add"     => "\Rwcoding\Examples\Pscc\Apis\User\Add",
                "user.edit"    => "\Rwcoding\Examples\Pscc\Apis\User\Edit",
                "user.del"     => "\Rwcoding\Examples\Pscc\Apis\User\Del",
                "user.restore" => "\Rwcoding\Examples\Pscc\Apis\User\Restore",

                "blog.list" => "\Rwcoding\Examples\Pscc\Apis\Blog\Lists",
                "blog.add"  => "\Rwcoding\Examples\Pscc\Apis\Blog\Add",
                "blog.edit" => "\Rwcoding\Examples\Pscc\Apis\Blog\Edit",
                "blog.del"  => "\Rwcoding\Examples\Pscc\Apis\Blog\Del",
            ]]
        ],
    ],
];