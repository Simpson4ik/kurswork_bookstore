<?php

namespace App\Core;

abstract class Controller
{
    // Задаємо макет за замовчуванням
    protected string $layout = 'layouts/main';

    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../../views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("View file not found: " . $view);
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($this->layout) {
            $layoutFile = __DIR__ . '/../../views/' . $this->layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                die("Layout file not found: " . $this->layout);
            }
        } else {
            echo $content;
        }
    }
}