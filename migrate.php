#!/usr/bin/env php
<?php

use app\core\database\Migrator;

include 'vendor/autoload.php';

$command = $argv[1] ?? null;

if (!in_array($command, ['up', 'down', 'make'])) {
    echo "Use: php migrate [up|down|make <name>]\n";
    exit(1);
}

const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR;
const MIGRATION_PATH = BASE_PATH . 'migrations' . DIRECTORY_SEPARATOR;

if (!is_dir(MIGRATION_PATH)) {
    mkdir(MIGRATION_PATH, 0755, true);
}

$config = require_once BASE_PATH . 'config/web.php';

$migrator = new Migrator($config['db'], MIGRATION_PATH);

switch ($command) {
    case 'up':
        $migrator->up();
        break;

    case 'down':
        $migrator->down();
        break;

    case 'make':
        $name = $argv[2] ?? 'migration';
        $file = 'm' . date('Y_m_d_His') . "_" . $name;

        $content = <<< PHP
        <?php
        
        namespace app\migrations;
        
        use app\core\database\Migration;
        
        class $file extends Migration
        {
        
            public function up(): void
            {
                // TODO insert code here
            }
        
            public function down(): void
            {
                // TODO insert code here
            }
            
        }
        PHP;

        file_put_contents(MIGRATION_PATH . $file . ".php", $content);

        echo "Migration created: $file\n";
        break;
}