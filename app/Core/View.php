<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], string $layout = 'app'): void
    {
        $viewPath = base_path('resources/views/' . str_replace('.', '/', $view) . '.php');
        $layoutPath = base_path('resources/views/layouts/' . $layout . '.php');

        if (! file_exists($viewPath)) {
            abort(500, 'View khong ton tai: ' . $view);
        }

        extract($data);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require $layoutPath;
    }
}
