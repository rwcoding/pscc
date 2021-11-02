<?php

namespace Rwcoding\Pscc\Core;

use Rwcoding\Pscc\Lang\Lang;
use Rwcoding\Pscc\Core\Web\RequestSwoole;
use Rwcoding\Pscc\Core\Web\Request;
use Rwcoding\Pscc\Core\Console\ConsoleRequest;
use Rwcoding\Pscc\Core\Web\Response;
use Rwcoding\Pscc\Core\Web\ResponseSwoole;
use Rwcoding\Pscc\Core\Console\ConsoleResponse;

/**
 * @property Request|RequestSwoole|ConsoleRequest $request
 * @property Response|ResponseSwoole|ConsoleResponse $response
 * @property PathFinderInterface $hook
 */
class Context implements PathFinderInterface
{
    /**
     * @var Request|RequestSwoole|ConsoleRequest
     */
    protected $request;

    /**
     * @var Response|ResponseSwoole|ConsoleResponse
     */
    protected $response;

    private ?PathFinderInterface $hook = null;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->init();
    }

    public function __get(string $name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        throw new \RuntimeException(Lang::t("property-not-found", $name));
    }

    public function init()
    {

    }

    /**
     * @return Request|RequestSwoole
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response|ResponseSwoole
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function getPathForRoute(): string
    {
        if ($this->hook) {
            return $this->hook->getPathForRoute();
        }
        return $this->request->getPathForRoute();
    }

    public function setHook($hook)
    {
        $this->hook = $hook;
    }

    public function getHook(): ?PathFinderInterface
    {
        return $this->hook;
    }

    public function setResponseBody($body)
    {
        $this->response->setBody($body);
    }

    public function emitResponse()
    {
        $this->response->send();
    }
}
