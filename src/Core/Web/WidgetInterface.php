<?php

namespace Rwcoding\Pscc\Core\Web;

interface WidgetInterface
{
    public function run(array $params): string ;
}