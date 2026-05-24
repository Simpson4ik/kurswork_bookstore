<?php

namespace App\Core;

abstract class Controller
{
    protected string $layout = 'layouts/main';

    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../../views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: " . $view);
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($this->layout) {
            $layoutFile = __DIR__ . '/../../views/' . $this->layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                throw new \RuntimeException("Layout file not found: " . $this->layout);
            }
        } else {
            echo $content;
        }
    }

    public function redirect(string $url): void
    {
        if (ob_get_length()) {
            ob_clean();
        }
        $base = defined('BASE_PATH') ? BASE_PATH : '';

        $url = ltrim($url, '/');

        header("Location: {$base}/{$url}");
        exit;
    }
}