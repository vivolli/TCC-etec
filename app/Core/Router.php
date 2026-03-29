<?php

namespace App\Core;

use Closure;

class Router
{
    /** @var array<int, array{methods:array<int,string>,pattern:string,handler:mixed}> */
    private array $routes = [];

    public function get(string $uri, $handler): self
    {
        return $this->add(['GET'], $uri, $handler);
    }

    public function post(string $uri, $handler): self
    {
        return $this->add(['POST'], $uri, $handler);
    }

    public function match(array $methods, string $uri, $handler): self
    {
        $upper = array_map('strtoupper', $methods);
        return $this->add($upper, $uri, $handler);
    }

    private function add(array $methods, string $uri, $handler): self
    {
        $normalized = $uri === '/' ? '/' : rtrim($uri, '/');
        $regex = preg_replace_callback('/{([^}]+)}/', function ($matches) {
            $param = trim($matches[1]);
            return '(?P<' . $param . '>[^/]+)';
        }, $normalized);

        if ($regex !== '/') {
            $regex .= '/?';
        }

        $pattern = '#^' . $regex . '$#i';

        $this->routes[] = [
            'methods' => $methods,
            'pattern' => $pattern,
            'handler' => $handler,
        ];

        return $this;
    }

    public function dispatch(string $method, string $uri): bool
    {
        $method = strtoupper($method);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'], true)) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($key) => !is_int($key),
                    ARRAY_FILTER_USE_KEY
                );
                $this->invoke($route['handler'], $params);
                return true;
            }
        }

        return false;
    }

    private function invoke($handler, array $params): void
    {
        if ($handler instanceof Closure || is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$controller, $method] = explode('@', $handler, 2);
            $controllerClass = $this->resolveController($controller);
            $instance = new $controllerClass();
            call_user_func_array([$instance, $method], $params);
            return;
        }

        throw new \RuntimeException('Handler de rota inválido.');
    }

    private function resolveController(string $controller): string
    {
        $controller = trim($controller, '\\');
        if (!str_starts_with($controller, 'App\\')) {
            $controller = 'App\\Http\\Controllers\\' . $controller;
        }
        if (!class_exists($controller)) {
            throw new \RuntimeException("Controller {$controller} não encontrado");
        }
        return $controller;
    }
}
