<?php 
class Router {
    private array $routes = [];
    private string $currentRouteName = '';

    public function get(mixed $path, mixed $action, array $options = []){   
        $this->add('GET', $path, $action, $options);
    }

    public function view(mixed $path, mixed $action, array $options = []){
        $this->add('GET', $path, $action, $options);
    }
    
    public function delete(mixed $path, mixed $action, array $options = []){   
        $this->add('DELETE', $path, $action, $options);
    }

    public function post(mixed $path, mixed $action, array $options = []){
        $this->add('POST', $path, $action, $options);
    }

    private function add(mixed $method, mixed $path, mixed $action, array $options = []){
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $action,
            'options' => $options
        ];
    }
 
    public function dispatch(mixed $method, mixed $uri){
        $uri = $this->cleanUri($uri);
        $start = microtime(true);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertRouteToRegex($route['path']);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $this->setCurrentRouteName($route['path']);
                $this->runAction($route['action'], $matches);

                $time = round((microtime(true) - $start) * 1000, 2);
                Logger::http_log($method, 'INFO', $uri, http_response_code(), $time);
                return;
            }
        }

        http_response_code(404);
        $time = round((microtime(true) - $start) * 1000, 2);
        Logger::http_log($method, 'ERROR', $uri, http_response_code(), $time);
        return view("404");
    }

    private function runAction(mixed $action, array $params){
        if (is_callable($action)) {
            return call_user_func_array($action, $params);
        }

        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);
            return (new $controller)->$method(...$params);
        }
    }

    private function setCurrentRouteName(string $routeName): void {
        $this->currentRouteName = $routeName;
    }

    private function getCurrentRouteName(): string {
        return $this->currentRouteName;
    }

    private function convertRouteToRegex(mixed $path){
        return "#^" . preg_replace('#\{[^}]+\}#', '([^/]+)', $path) . "$#";
    }

    private function cleanUri(mixed $uri){
        $path = parse_url((string) $uri, PHP_URL_PATH) ?: '/';

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = dirname($scriptName);
        $basePath = rtrim($basePath, '/');

        if ($basePath !== '.' && $basePath !== '/' && $basePath !== '' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        return $path === '' ? '/' : $path;
    }
}