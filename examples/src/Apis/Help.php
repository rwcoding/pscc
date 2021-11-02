<?php

namespace Rwcoding\Examples\Pscc\Apis;

class Help extends Base
{
    public function index()
    {
        echo "help\n";
        echo " -- user.list \n";
        echo " -- user.add \n";
        echo " -- user.edit \n";
        echo " -- user.del \n";
    }
}