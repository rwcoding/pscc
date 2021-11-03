<?php

namespace Rwcoding\Pscc\Core;

interface ControllerInterface
{
    /**
     * @param Context|PathFinderInterface $context
     * @return mixed
     */
    public function __construct($context);
}