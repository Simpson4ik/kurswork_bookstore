<?php

namespace App\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function setStatus(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }
    public function addHeader(string $header): self
    {
        $this->headers[] = $header;
        return $this;
    }

    public function send(string $content = ''): void
    {
        if ($content !== '') {
            $this->content = $content;
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $header) {
            header($header);
        }

        if (ob_get_level() > 0) {
            ob_clean();
        }

        echo $this->content;
        exit;
    }
    public function json(array $data, int $status = 200): void
    {
        $this->setStatus($status)
            ->addHeader('Content-Type: application/json; charset=utf-8')
            ->send(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}