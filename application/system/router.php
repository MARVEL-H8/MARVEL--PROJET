<?php
// system/Router.php

class Router {
    private $routes = [];
    
    public function get($path, $callback, $middlewares = []) {
        $this->addRoute('GET', $path, $callback, $middlewares);
    }
    
    public function post($path, $callback, $middlewares = []) {
        $this->addRoute('POST', $path, $callback, $middlewares);
    }
    
    private function addRoute($method, $path, $callback, $middlewares) {
        // Conversion du chemin en expression régulière pour capturer les paramètres
        $pattern = preg_replace('/\/{([^\/]+)}/', '/(?P<$1>[^/]+)', $path);
        $pattern = "#^{$pattern}$#";
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'callback' => $callback,
            'middlewares' => $middlewares
        ];
    }
    
    public function resolve() {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            
            if (preg_match($route['pattern'], $requestUri, $matches)) {
                // Extraction des paramètres de l'URL
                $params = array_filter($matches, function($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);
                
                // Exécution des middlewares
                foreach ($route['middlewares'] as $middleware) {
                    if (!$middleware->handle()) {
                        return;
                    }
                }
                
                // Exécution du callback
                $this->executeCallback($route['callback'], $params);
                return;
            }
        }
        
        // Route non trouvée
        http_response_code(404);
        require_once __DIR__ . '/../application/views/errors/404.php';
    }
    
    private function executeCallback($callback, $params) {
        if (is_callable($callback)) {
            call_user_func_array($callback, $params);
        } else if (is_string($callback)) {
            // Format: "ControllerName@methodName"
            list($controller, $method) = explode('@', $callback);
            
            if (class_exists($controller)) {
                $controllerInstance = new $controller();
                
                if (method_exists($controllerInstance, $method)) {
                    call_user_func_array([$controllerInstance, $method], $params);
                } else {
                    throw new Exception("Méthode {$method} non trouvée dans le contrôleur {$controller}");
                }
            } else {
                throw new Exception("Contrôleur {$controller} non trouvé");
            }
        } else {
            throw new Exception("Type de callback non valide");
        }
    }
}