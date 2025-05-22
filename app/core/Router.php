<?php
/**
 * Router Class
 * Uses AltoRouter for routing
 */

require_once BASE_PATH . '/vendor/altorouter/altorouter/AltoRouter.php';

class Router {
    private static $instance = null;
    private $router;

    /**
     * Constructor - creates a new AltoRouter instance
     */
    private function __construct() {
        $this->router = new AltoRouter();
        // Set the base path for the router
        $this->router->setBasePath(parse_url(BASE_URL, PHP_URL_PATH));
    }

    /**
     * Singleton pattern implementation
     * @return Router
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the AltoRouter instance
     * @return AltoRouter
     */
    public function getRouter() {
        return $this->router;
    }

    /**
     * Add a GET route
     * @param string $route Route pattern
     * @param string $target Target (controller@method)
     * @param string $name Route name
     * @return Router
     */
    public function get($route, $target, $name = null) {
        $this->router->map('GET', $route, $target, $name);
        return $this;
    }

    /**
     * Add a POST route
     * @param string $route Route pattern
     * @param string $target Target (controller@method)
     * @param string $name Route name
     * @return Router
     */
    public function post($route, $target, $name = null) {
        $this->router->map('POST', $route, $target, $name);
        return $this;
    }

    /**
     * Add a PUT route
     * @param string $route Route pattern
     * @param string $target Target (controller@method)
     * @param string $name Route name
     * @return Router
     */
    public function put($route, $target, $name = null) {
        $this->router->map('PUT', $route, $target, $name);
        return $this;
    }

    /**
     * Add a DELETE route
     * @param string $route Route pattern
     * @param string $target Target (controller@method)
     * @param string $name Route name
     * @return Router
     */
    public function delete($route, $target, $name = null) {
        $this->router->map('DELETE', $route, $target, $name);
        return $this;
    }

    /**
     * Add a route with custom HTTP methods
     * @param array|string $methods HTTP method(s)
     * @param string $route Route pattern
     * @param string $target Target (controller@method)
     * @param string $name Route name
     * @return Router
     */
    public function map($methods, $route, $target, $name = null) {
        $this->router->map($methods, $route, $target, $name);
        return $this;
    }

    /**
     * Match the current request and dispatch to the matching route
     */
    public function dispatch() {
        // Match current request
        $match = $this->router->match();

        // No route matched
        if (!$match) {
            // Handle 404 Not Found
            header("HTTP/1.0 404 Not Found");
            include VIEW_PATH . '/errors/404.php';
            exit;
        }

        // Get controller and method from the target
        list($controller, $method) = explode('@', $match['target']);

        // Check if controller exists
        if (!file_exists(APP_PATH . "/controllers/{$controller}.php")) {
            // Handle 404 Not Found
            header("HTTP/1.0 404 Not Found");
            include VIEW_PATH . '/errors/404.php';
            exit;
        }

        // Include controller
        require_once APP_PATH . "/controllers/{$controller}.php";

        // Check if class exists
        if (!class_exists($controller)) {
            // Handle 500 Internal Server Error
            header("HTTP/1.0 500 Internal Server Error");
            include VIEW_PATH . '/errors/500.php';
            exit;
        }

        // Create controller instance
        $controllerInstance = new $controller();

        // Check if method exists
        if (!method_exists($controllerInstance, $method)) {
            // Handle 500 Internal Server Error
            header("HTTP/1.0 500 Internal Server Error");
            include VIEW_PATH . '/errors/500.php';
            exit;
        }

        // Call controller method with route parameters
        call_user_func_array([$controllerInstance, $method], $match['params']);
    }

    /**
     * Generate a URL for a named route
     * @param string $name Route name
     * @param array $params Route parameters
     * @return string
     */
    public function generate($name, $params = []) {
        return $this->router->generate($name, $params);
    }
} 