<?php

namespace Rwcoding\Pscc\Core\Web;

class Response
{
    use ResponseTrait;

    public function send()
    {
        if ($this->status) {
            http_response_code($this->status);
        }
        if ($this->cookies) {
            foreach ($this->cookies as $name => $cookie) {
                setcookie($name, $cookie['value'], [
                    'expires'  => $cookie['expire'] ?? 0,
                    'path'     => $cookie['path'] ?? '/',
                    'domain'   => $cookie['domain'] ?? '',
                    'secure'   => $cookie['secure'] ?? false,
                    'httponly' => $cookie['httponly'] ?? true,
                    'samesite' => $cookie['samesite'] ?? '',
                ]);
            }
        }
        if ($this->headers) {
            foreach ($this->headers as $k => $v) {
                header(ucfirst($k) . ': ' . $v);
            }
        }

        if (is_array($this->body)) {
            echo json_encode($this->body, JSON_UNESCAPED_UNICODE);
        } else {
            echo $this->body;
        }
    }

    public function redirect(string $url, $code = 302): self
    {
        $this->addHeader('Location', $url)->setStatus($code);
        return $this;
    }
}