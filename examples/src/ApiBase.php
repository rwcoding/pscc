<?php

namespace Rwcoding\Examples\Pscc;

use Rwcoding\Pscc\Core\ApiInterface;
use Rwcoding\Pscc\Di;

abstract class ApiBase implements ApiInterface
{
    private ApiContext $context;

    public array $rules = [];

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function __get(string $name)
    {
        return $this->getData()[$name] ?? null;
    }

    public function getContext(): ApiContext
    {
        return $this->context;
    }

    public function getData(): array
    {
        return $this->context->getData();
    }

    public function success(array $data = [], $msg = '')
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

    public function vError(): string
    {
        $validator = Di::my()->validator->make($this->context->getData(), $this->rules);
        if ($validator->fails()) {
            return implode(";", $validator->errors()->all());
        }
        return "";
    }

    public function run()
    {
        if ($err = $this->vError()) {
            return $this->failure($err);
        }
        return $this->handle();
    }

    abstract public function handle();
}