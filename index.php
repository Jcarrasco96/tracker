<?php

use app\core\App;
use app\core\helpers\ArrayHelper;

include 'vendor/autoload.php';

defined('APP_ENV') or define('APP_ENV', 'dev');

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('error_log', 'log/error_' . date('Ymd') . '.log');

if ($_SERVER['REQUEST_URI'] == '/api/e') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR;
const VIEWS_PATH = BASE_PATH . DIRECTORY_SEPARATOR . 'views';

$config = ArrayHelper::merge(
    require_once BASE_PATH . 'config/web.php',
    require_once BASE_PATH . 'config/web.local.php',
);

$app = new App($config);

$app->get('/site/index', 'SiteController@actionIndex');
$app->post('/site/select-role/{role}', 'SiteController@actionSelectRole');

$app->get('/auth/login', 'AuthController@actionLogin');
$app->post('/auth/login', 'AuthController@actionLogin');
$app->post('/auth/logout', 'AuthController@actionLogout');
//$app->get('/auth/register', 'AuthController@actionRegister');

$app->post('/api/e', 'ApiController@actionTrack');
$app->get('/api/script', 'ApiController@actionScript');

$app->get('/website/{website:uuid}', 'WebsiteController@actionDetails');
$app->get('/website/create', 'WebsiteController@actionCreate');

$app->post('/website/create', 'WebsiteController@actionCreate');

$app->delete('/website/{website:uuid}', 'WebsiteController@actionDelete');

$app->run();