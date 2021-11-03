<?php

namespace Rwcoding\Pscc\Core;

interface ApiInterface
{
    public function __construct($context);
    public function run();
}