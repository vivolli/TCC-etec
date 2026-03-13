<?php
// Simple Noticias controller — renders views in app/Views/Noticias
class NoticiasController {
    public static function dispatch(string $name) {
        $safe = basename($name);
        $view = __DIR__ . '/../Views/Noticias/' . $safe . '.php';
        if (file_exists($view)) {
            include $view;
            return true;
        }
        http_response_code(404);
        echo "Notícia não encontrada.";
        return false;
    }
}
