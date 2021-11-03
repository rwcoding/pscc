<?php

namespace Rwcoding\Pscc\Core;

interface ResponseInterface
{
    public function setBody($body);
    public function getBody();
    public function send();
}