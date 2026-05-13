<?php

declare(strict_types=1);

namespace app\migrations;

use app\core\database\Migration;

class m2026_05_07_183157_add_columns_event extends Migration
{

    public function up(): void
    {
        $this->db->exec("ALTER TABLE `event`
            ADD COLUMN `browser` VARCHAR(20) NULL DEFAULT NULL,
            ADD COLUMN `os` VARCHAR(20) NULL DEFAULT NULL,
            ADD COLUMN `device_type` VARCHAR(20) NULL DEFAULT NULL;
        ");
    }

    public function down(): void
    {
        $this->db->exec("ALTER TABLE `event`
            DROP COLUMN `browser`,
            DROP COLUMN `os`,
            DROP COLUMN `device_type`;
        ");
    }
    
}