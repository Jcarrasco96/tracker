<?php

use app\core\App;

include 'vendor/autoload.php';

defined('APP_ENV') or define('APP_ENV', 'dev');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'log/error_' . date('Ymd') . '.log');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR;
const VIEWS_PATH = BASE_PATH . DIRECTORY_SEPARATOR . 'views';

$config = require_once BASE_PATH . 'config/web.php';

$app = new App($config);

$app->get('/site/index', 'SiteController@actionIndex');
$app->get('/site/website/{website}', 'SiteController@actionWebsite');
$app->post('/site/select-role/{role}', 'SiteController@actionSelectRole');

$app->get('/auth/login', 'AuthController@actionLogin');
$app->post('/auth/login', 'AuthController@actionLogin');
$app->post('/auth/logout', 'AuthController@actionLogout');

$app->post('/api/e', 'ApiController@actionTrack');

$app->get('/auth/register', 'AuthController@actionRegister');

$app->get('/website/create', 'WebsiteController@actionCreate');
$app->post('/website/create', 'WebsiteController@actionCreate');

$app->delete('/website/{website}', 'WebsiteController@actionDelete');

try {
    $app->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
