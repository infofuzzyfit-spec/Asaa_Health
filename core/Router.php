<?php

/**
 * Router Class
 * URL routing and request handling
 */

class Router
{
    private $routes = [];
    private $middleware = [];

    public function get($path, $handler, $middleware = [])
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post($path, $handler, $middleware = [])
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put($path, $handler, $middleware = [])
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete($path, $handler, $middleware = [])
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    private function addRoute($method, $path, $handler, $middleware = [])
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function middleware($middleware)
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Dispatch the request
     */
    public function dispatch(Request $request, Response $response)
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        
        // Remove query string
        $uri = strtok($uri, '?');
        
        // Find matching route
        foreach ($this->routes[$method] as $route => $handler) {
            if ($this->matchRoute($route, $uri)) {
                return $this->executeHandler($handler, $request, $response, $this->extractParams($route, $uri));
            }
        }
        
        // No route found
        return $response->json(['error' => 'Route not found'], 404);
    }

    private function matchRoute($route, $uri)
    {
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';
        return preg_match($routePattern, $uri);
    }

    private function extractParams($route, $uri)
    {
        $routePattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';

        if (preg_match($routePattern, $uri, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return [];
    }

    private function executeHandler($handler, Request $request, Response $response, $params)
    {
        // Apply middleware
        $middleware = $handler['middleware'] ?? [];
        
        $next = function($req, $res) use ($handler, $params) {
            if (is_string($handler['handler'])) {
                list($controller, $method) = explode('@', $handler['handler']);
                $controllerClass = 'App\\Controllers\\' . $controller . 'Controller';

                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $method)) {
                        return call_user_func_array([$controllerInstance, $method], [$req, $res, $params]);
                    }
                }
            } elseif (is_callable($handler['handler'])) {
                return call_user_func_array($handler['handler'], [$req, $res, $params]);
            }
            
            return $res->json(['error' => 'Handler not found'], 404);
        };

        // Apply middleware in reverse order
        foreach (array_reverse($middleware) as $middlewareClass) {
            $middlewareInstance = new $middlewareClass();
            $next = function($req, $res) use ($middlewareInstance, $next) {
                return $middlewareInstance->handle($req, $res, $next);
            };
        }

        return $next($request, $response);
    }
}