<?php

namespace Hnqca\Router;

use ReflectionMethod;

class Router
{
    private $allowedMethods = [
        "GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"
    ];

    private int|null $error         = null;
    private string   $requestMethod = "";
    private string   $requestURI    = "";
    private array    $routes        = [];
    private array    $placeholdersValues = [];

    public function __construct()
    {
        $this->requestURI    = $this->captureURI();
        $this->requestMethod = $this->captureRequestMethod();
    }

    public function error(): int|null
    {
        return $this->error;
    }
    
    private function setError(int $code): void
    {
        $this->error = $code;
    }

    private function captureRequestMethod(): string
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        if (!in_array($method, $this->allowedMethods)) {
            $this->setError(405); // Method Not Allowed
        }

        return $method;
    }

    private function captureURI(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);
        $uri = $uri['path'];

        return $uri;
    }

    public function __call($method, $args): Router
    {
        $this->addRoute(strtoupper($method), ...$args);

        return $this;
    }

    private function addRoute($method, $endpoint, $controller = [], $callbackMiddleware = null): void
    {
        $newRoute = [
            "endpoint"           => $endpoint,
            "class"              => $controller[0] ?? null,
            "method"             => $controller[1] ?? null,
            "callbackMiddleware" => $callbackMiddleware
        ];

        $this->routes[$method][] = $newRoute;
    }

    public function dispatch()
    {
        if ($this->error()) {
            return;
        }
    
        $route = $this->findRoute();
        
        if (!$route) {
            $this->setError(404); // Not Found.
            return;
        }

        $this->extractPlaceholdersValues($route['endpoint']);

        $this->executeMiddleware($route['callbackMiddleware']);
        $this->executeControllerMethod($route);
    }

    private function executeMiddleware($middleware)
    {
        if ($middleware) {
            echo call_user_func($middleware, (new Request($this->placeholdersValues)), (new Response));
        }
    }

    private function executeControllerMethod($route)
    {
        $class  = $route['class'];
        $method = $route['method'];

        if (!$class && !$method) {
            return;
        }

        if (!class_exists($class)) {
            $this->setError(501); // Not Implemented
            return;
        }

        if (!method_exists($class, $method)) {
            $this->setError(501); // Not Implemented
            return;
        }

        $reflectionMethod = new ReflectionMethod($class, $method);

        if (!$reflectionMethod->isPublic()) {
            $this->setError(501); // Not Implemented
            return;
        }

        echo (new $class)->$method(new Request($this->placeholdersValues), (new Response));
    }

    private function findRoute(): array|null
    {
        $routes = $this->routes[$this->requestMethod] ?? [];

        $route = array_filter($routes, function ($checkRoute) {

            $pattern = preg_replace('#\{.*?\}#', '([^/]+)', $checkRoute['endpoint']);
            $pattern = '#^' . $pattern . '/?$#';

            return preg_match($pattern, $this->requestURI);
        });

        return $route ? reset($route) : null;
    }

    private function extractPlaceholdersValues(string $endpoint)
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_-]+)\}/', '(?<$1>[^/]+)', $endpoint);
        $pattern = '#^' . $pattern . '/?$#';

        preg_match($pattern, $this->requestURI, $matches);

        // Filter only the values corresponding to the named placeholders.
        $values = array_intersect_key($matches, array_flip(array_filter(array_keys($matches), 'is_string')));

        $this->placeholdersValues = $values;
    }

    public function redirect(string $endpoint, int $code = 0)
    {
        return (new Response)->redirect($endpoint, $code);
    }
}