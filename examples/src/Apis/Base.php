<?php

namespace Rwcoding\Examples\Pscc\Apis;

use Rwcoding\Pscc\Core\Context;
use Rwcoding\Pscc\Core\ControllerInterface;
use Rwcoding\Examples\Pscc\ApiContext;

/**
 * @property ApiContext|Context $context
 * @property array $data
 *
 */
class Base implements ControllerInterface
{
    /**
     * @var ApiContext|Context
     */
    private $context;

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getData()
    {
        return $this->context->getData();
    }

    public function success(array $data, $msg = '')
    {
        return $this->context->response->setBody(["code"=>1, "msg"=>$msg, "data"=>$data]);
    }

    public function failure($msg, $data = [])
    {
        if ($data) {
            return $this->context->response->setBody(["code"=>0, "msg"=>$msg, "data"=>$data]);
        } else {
            return $this->context->response->setBody(["code"=>0, "msg"=>$msg]);
        }
    }
}