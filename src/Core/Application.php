<?php

namespace Rwcoding\Pscc\Core;

use Rwcoding\Pscc\Di;
use Rwcoding\Pscc\Lang\Lang;

class Application
{
    private array $params = [];

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function run(Context $context)
    {
        $cb = Di::my()->router->find($context->getPathForRoute());
        if (!$cb) {
            throw (new \RuntimeException(Lang::t("route-not-found",$context->getPathForRoute())));
        }

        if (!is_array($cb) && is_callable($cb)) {
            call_user_func($cb, $context);
            return;
        }

        $class = $cb[0];
        $method = $cb[1];

        $object = new $class;
        if ($object instanceof ControllerInterface) {
            $object->setContext($context);
            if (method_exists($object, 'beforeAction') && !$object->beforeAction($method)) {
                $context->emitResponse();
                return;
            }
            $beforeName = 'before' . $method;
            if (method_exists($object, $beforeName) && !$object->$beforeName()) {
                $context->emitResponse();
                return;
            }
            $ret = $object->$method($cb[2]??null);
        } else {
            if (method_exists($object, 'beforeAction') && !$object->beforeAction($context, $method)) {
                $context->emitResponse();
                return;
            }
            $beforeName = 'before' . $method;
            if (method_exists($object, $beforeName) && !$object->$beforeName($context)) {
                $context->emitResponse();
                return;
            }
            $ret = $object->$method($context, $cb[2]??null);
        }
        if ($ret !== null) {
            $context->setResponseBody($ret);
        }
        $context->emitResponse();
    }
}
