<?php

namespace app\core;

use app\core\services\ExceptionHandler;
use app\core\services\Logger;
use app\core\services\Request;
use app\core\services\Router;
use app\core\services\Session;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class App
{

    public static array $config = [
        'name' => 'Tracker',
        'version' => '0.1',
        'timezone' => 'America/New_York',
    ];

    private Router $router;
    private Request $request;

    public static Session $session;
    public static Logger $logger;

    public function __construct(array $config = [])
    {
        ExceptionHandler::register();

        defined('APP_ENV') or define('APP_ENV', 'prod');

        define('APP_ROOT', getcwd() . DIRECTORY_SEPARATOR);

        define('APP_RUNTIME', APP_ROOT . 'runtime' . DIRECTORY_SEPARATOR);

        define('APP_LOGS_FOLDER', APP_RUNTIME . 'logs' . DIRECTORY_SEPARATOR);
        define('APP_RATE_LIMIT_FOLDER', APP_RUNTIME . 'rate_limit' . DIRECTORY_SEPARATOR);

        if (!is_dir(APP_LOGS_FOLDER)) {
            mkdir(APP_LOGS_FOLDER, 0755, true);
        }
        if (!is_dir(APP_RATE_LIMIT_FOLDER)) {
            mkdir(APP_RATE_LIMIT_FOLDER, 0755, true);
        }

        self::$config = array_merge(self::$config, $config);
        self::$logger = new Logger(APP_LOGS_FOLDER . 'app.log');
        self::$session = Session::getInstance();

        date_default_timezone_set(self::$config['timezone']);

        $this->router = new Router();
        $this->request = new Request();
    }

    public function get(string $route, string $action): void
    {
        $this->router->addRoute(Router::ROUTER_GET, $route, $action);
    }

    public function post(string $route, string $action): void
    {
        $this->router->addRoute(Router::ROUTER_POST, $route, $action);
    }

    public function delete(string $route, string $action): void
    {
        $this->router->addRoute(Router::ROUTER_DELETE, $route, $action);
    }

    /**
     * @throws Exception
     */
    #[NoReturn]
    public function run(): void
    {
        $this->router->dispatch($this->request);
    }

}