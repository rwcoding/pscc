<?php

namespace Rwcoding\Pscc\Core\Web\Session;

/**
 * session 文件存储处理
 */
class SessionFileHandler implements SessionHandlerInterface
{
    /**
     * @var Session session组件
     */
    private Session $session;

    /**
     * @var string 存储session文件的路径
     */
    private string $path;

    /**
     * @var string session文件前缀
     */
    private string $prefix;

    public function __construct(Session $session, string $path, string $prefix = 'sess_php_')
    {
        $this->session = $session;
        $this->path = $path;
        $this->prefix = $prefix;
    }

    public function getFile(): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->prefix . $this->session->sessionId();
    }

    public function data(): array
    {
        $file = $this->getFile();
        $fileExists = file_exists($file);
        if ($fileExists) {
            if (@filemtime($file) < time() - $this->session->getTimeout()) {
                @unlink($file);
                $fileExists = false;
            }
        }
        if ($fileExists) {
            $content = unserialize(file_get_contents($file));
            if (isset($content['expire']) && $content['expire'] > time()) {
                return $content;
            } else {
                @unlink($file);
            }
        }
        return [];
    }

    public function store(): bool
    {
        return (bool) file_put_contents($this->getFile(), serialize($this->session->getData()));
    }

    public function destroy(): void
    {
        $file = $this->getFile();
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    public function gc(): void
    {
        if ($dh = @opendir($this->path)) {
            $expire = time() - $this->session->getTimeout();
            while (($file = readdir($dh)) !== false) {
                if (strncmp($file, $this->prefix, strlen($this->prefix)) == 0) {
                    if (filemtime($this->path . DIRECTORY_SEPARATOR . $file) < $expire) {
                        unlink($this->path . DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
            closedir($dh);
        }
    }
}
