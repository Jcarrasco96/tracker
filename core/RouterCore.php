<?php

namespace app\core;

use JetBrains\PhpStorm\Deprecated;
use ReflectionException;
use ReflectionMethod;

#[Deprecated]
class RouterCore
{

    private array $afterRoutes = [];

    private array $beforeRoutes = [];

    protected ?object $notFoundCallback = null;

    private string $baseRoute = '';

    private ?string $serverBasePath = null;

    private string $namespace = '';

    public function before(string $methods, string $pattern, callable|string $fn): void
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            $this->beforeRoutes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn,
            ];
        }
    }

    public function match(string $methods, string $pattern, callable|string $fn): void
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            $this->afterRoutes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn,
            ];
        }
    }

    public function all(string $pattern, callable|string $fn): void
    {
        $this->match('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pattern, $fn);
    }

    public function get(string $pattern, callable|string $fn): void
    {
        $this->match('GET', $pattern, $fn);
    }

    public function post(string $pattern, callable|string $fn): void
    {
        $this->match('POST', $pattern, $fn);
    }

    public function patch(string $pattern, callable|string $fn): void
    {
        $this->match('PATCH', $pattern, $fn);
    }

    public function delete(string $pattern, callable|string $fn): void
    {
        $this->match('DELETE', $pattern, $fn);
    }

    public function put(string $pattern, callable|string $fn): void
    {
        $this->match('PUT', $pattern, $fn);
    }

    public function options(string $pattern, callable|string $fn): void
    {
        $this->match('OPTIONS', $pattern, $fn);
    }

    public function mount(string $baseRoute, callable $fn): void
    {
        $curBaseRoute = $this->baseRoute;

        $this->baseRoute .= $baseRoute;

        call_user_func($fn);

        $this->baseRoute = $curBaseRoute;
    }

    public function requestHeaders(): false|array
    {
        $headers = [];

        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            if ($headers !== false) {
                return $headers;
            }
        }

        foreach ($_SERVER as $name => $value) {
            if ((str_starts_with($name, 'HTTP_')) || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace([' ', 'Http'], ['-', 'HTTP'], ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    public function requestMethod(): string
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = $this->requestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ['PUT', 'DELETE', 'PATCH'])) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }

    /**
     * @throws ReflectionException
     */
    public function run(callable|string $callback = null): bool
    {
        $requestedMethod = $this->requestMethod();

        if (isset($this->beforeRoutes[$requestedMethod])) {
            $this->handle($this->beforeRoutes[$requestedMethod]);
        }

        $numHandled = false;
        if (isset($this->afterRoutes[$requestedMethod])) {
            $numHandled = $this->handle($this->afterRoutes[$requestedMethod], true);
        }

        if (!$numHandled) {
            $this->trigger404();
        } else {
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        return $numHandled;
    }

    public function set404(callable|object $fn): void
    {
        $this->notFoundCallback = $fn;
    }

    /**
     * @throws ReflectionException
     */
    public function trigger404(): void
    {
        if ($this->notFoundCallback) {
            $this->invoke($this->notFoundCallback);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        }
    }

    /**
     * @throws ReflectionException
     */
    private function handle(array $routes, bool $quitAfterRun = false): bool
    {
        $handled = false;

        $uri = $this->currentUri();

        foreach ($routes as $route) {
            $route['pattern'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['pattern']);

            if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                $matches = array_slice($matches, 1);

                $params = array_map(function ($match, $index) use ($matches) {

                    if (isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        if ($matches[$index + 1][0][1] > -1) {
                            return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                        }
                    }

                    return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));

                $this->invoke($route['fn'], $params);

                $handled = true;

                if ($quitAfterRun) {
                    break;
                }
            }
        }

        return $handled;
    }

    /**
     * @throws ReflectionException
     */
    private function invoke(callable|string $fn, $params = array()): void
    {
        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        } elseif (stripos($fn, '@') !== false) {
            list($controller, $method) = explode('@', $fn);

            if ($this->namespace !== '') {
                $controller = $this->namespace . '\\' . $controller;
            }

            if (is_callable(array($controller, $method))) {
                if ((new ReflectionMethod($controller, $method))->isStatic()) {
                    forward_static_call_array(array($controller, $method), $params);
                } else {
                    if (is_string($controller)) {
                        $controller = new $controller();
                    }
                    call_user_func_array(array($controller, $method), $params);
                }
            }
        }
    }

    public function currentUri(): string
    {
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($this->basePath()));

        if (str_contains($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        return '/' . trim($uri, '/');
    }

    public function basePath(): string
    {
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }

        return $this->serverBasePath;
    }

}