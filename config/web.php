<?php

$localConfig = require(__DIR__ . '/web.local.php');

return array_merge([
    'name' => 'Tracker',
    'db' => [
        'driver' => 'mysql',
        'mysql' => [
            'host' => 'localhost',
            'user' => '',
            'password' => '',
            'dbname' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ],
    ],
], $localConfig);