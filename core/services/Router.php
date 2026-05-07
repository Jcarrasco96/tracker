<?php

namespace app\core\services;

use app\core\exceptions\NotFoundHttpException;
use Exception;
use JetBrains\PhpStorm\ExpectedValues;

class Router
{

    const ROUTER_DELETE = 'DELETE';
    const ROUTER_GET = 'GET';
    //const ROUTER_HEAD = 'HEAD';
    //const ROUTER_OPTIONS = 'OPTIONS';
    //const ROUTER_PATCH = 'PATCH';
    const ROUTER_POST = 'POST';
    //const ROUTER_PUT = 'PUT';

    private array $routes = [];

    public function addRoute(#[ExpectedValues([self::ROUTER_GET, self::ROUTER_POST, self::ROUTER_DELETE])] string $method, string $route, string $action): void
    {
        if (in_array($method, [
            self::ROUTER_DELETE,
            self::ROUTER_GET,
            //self::ROUTER_HEAD,
            //self::ROUTER_OPTIONS,
            //self::ROUTER_PATCH,
            self::ROUTER_POST,
            //self::ROUTER_PUT,
        ])) {
            $this->routes[$method][$route] = $action;
        }
    }

    /**
     * @throws Exception
     */
    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        if ($path == '/') {
            Response::redirect('site/index');
//            $path = '/site/index';
        }

        $matchedRoute = $this->findConfiguredRoute($method, $path);

        if ($matchedRoute) {
            $this->callHandler($matchedRoute['handler'], $request, $matchedRoute['params']);
            exit();
        }

        throw new NotFoundHttpException("Route not found: $path");
    }

    /**
     * @throws Exception
     */
    private function callHandler(string $handler, Request $request, $params = []): void
    {
        list($controllerName, $actionName) = explode('@', $handler);

        $controllerName = "app\\controllers\\$controllerName";

        if (!class_exists($controllerName)) {
            throw new Exception("Controller $controllerName not found");
        }

        $controller = new $controllerName($request);

        if (!method_exists($controller, $actionName)) {
            throw new Exception("Action $actionName not found in $controllerName");
        }

        $response = $controller->createAction($actionName, $params);

//        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; frame-ancestors 'self';");

        if ($response instanceof Response) {
            $response->send();
        } elseif (is_array($response) || is_object($response)) {
            Response::json($response)->send();
        } else {
            echo $response;
        }
    }

    private function findConfiguredRoute(string $method, string $path): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $routePath => $handler) {
            $params = $this->matchAndValidateRoute($routePath, $path);
            if ($params !== false) {
                return [
                    'handler' => $handler,
                    'params' => $params
                ];
            }
        }

        return null;
    }

    private function matchAndValidateRoute(int|string $routePath, string $requestPath): false|array
    {
        $pattern = $this->buildRoutePattern($routePath);

        if (preg_match($pattern, $requestPath, $matches)) {
            $params = [];
            $paramDefinitions = $this->extractParamDefinitions($routePath);

            foreach ($paramDefinitions as $paramName => $paramType) {
                if (isset($matches[$paramName])) {
                    $value = $matches[$paramName];

                    if (!$this->validateParamType($value, $paramType)) {
                        return false;
                    }

                    $params[$paramName] = $this->castParamValue($value, $paramType);
                }
            }

            return $params;
        }

        return false;
    }

    private function buildRoutePattern(int|string $routePath): string
    {
        $pattern = preg_replace_callback('/\{(\w+)(?::(\w+))?}/', function ($matches) {
            $paramType = $matches[2] ?? 'string';

            $patterns = [
                'int' => '(\d+)',
                'integer' => '(\d+)',
                'number' => '(\d+)',
                'float' => '([\d\.]+)',
                'string' => '([^/]+)',
                'slug' => '([a-z0-9-]+)',
                'alpha' => '([a-zA-Z]+)',
                'alphanumeric' => '([a-zA-Z0-9]+)',
                'base64' => '([A-Za-z0-9+\/]+={0,2})',
            ];

            $regex = $patterns[$paramType] ?? '([^/]+)';

            return "(?P<$matches[1]>$regex)";
        }, $routePath);

        return '#^' . str_replace('/', '\/', $pattern) . '$#';
    }

    private function extractParamDefinitions(int|string $routePath): array
    {
        $definitions = [];

        preg_match_all('/\{(\w+)(?::(\w+))?}/', $routePath, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $paramName = $match[1];
            $paramType = $match[2] ?? 'string';
            $definitions[$paramName] = $paramType;
        }

        return $definitions;
    }

    private function validateParamType(string $value, string $paramType): bool
    {
        return match ($paramType) {
            'int', 'integer' => ctype_digit($value),
            'float', 'number' => is_numeric($value),
            'alpha' => ctype_alpha($value),
            'alphanumeric' => ctype_alnum($value),
            'slug' => (bool)preg_match('/^[a-z0-9-]+$/', $value),
            'base64' => (bool)base64_decode($value, true),
            default => true,
        };
    }

    private function castParamValue(string $value, string $type): float|int|string
    {
        return match ($type) {
            'int', 'integer' => (int)$value,
            'float', 'number' => (float)$value,
            default => $value,
        };
    }

}