<?php

namespace Rwcoding\Pscc\Core\Web;

use Rwcoding\Pscc\Core\Context;
use Rwcoding\Pscc\Core\ControllerInterface;
use Rwcoding\Pscc\Core\PathFinderInterface;
use Rwcoding\Pscc\Di;
use Rwcoding\Pscc\Core\Meta;
use Rwcoding\Pscc\Core\Web\Session\Session;
use Rwcoding\Pscc\Core\Web\Session\SessionHandlerInterface;

/**
 * @property string $controllerId
 * @property Session $session
 * @property Request|RequestSwoole $request
 * @property Response|ResponseSwoole $response
 * @property Context|PathFinderInterface $context
 */
class WebController extends Meta implements ControllerInterface
{
    /**
     * @var null|Context|PathFinderInterface
     */
    private $context = null;

    public function __construct($context)
    {
        // parent::__construct();
        if ($this->context) {
            return;
        }
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return Request|RequestSwoole
     */
    public function getRequest()
    {
        return $this->context->getRequest();
    }

    /**
     * @return Response|ResponseSwoole
     */
    public function getResponse()
    {
        return $this->context->getResponse();
    }

    public function getControllerId(): string
    {
        $controllerId = $this->get('controllerId');
        if (!$controllerId) {
            $class = static::class;
            $className = substr($class, strrpos($class, '\\') + 1);
            $controllerId = lcfirst(str_replace("Controller", "", $className));
            $this->set('controllerId', $controllerId);
        }
        return $controllerId;
    }

    protected function getSessionHandler($session): ?SessionHandlerInterface
    {
        return null;
    }

    protected function getSessionConfig(): array
    {
        return Di::my()->config->session ?? [];
    }

    protected function getSession(): Session
    {
        $session = $this->get('session');
        if (!$session) {
            $sessionConfig = $this->getSessionConfig();
            $cookieName = $sessionConfig['cookieName'] ?? 'HTYS_SESS';
            $session = new Session($this->context->getRequest()->getCookie($cookieName));
            $session->setHandler($this->getSessionHandler($session))
                ->setTimeout($sesionConfig['timeout'] ?? 86400 * 7)
                ->setSender(function ($id, $expire) use ($cookieName) {
                    $this->context->getResponse()->addCookie($cookieName, [
                        'value'  => $id,
                        'expire' => $expire,
                    ]);
                });
            $this->set('session', $session);
        }
        return $session;
    }
}