<?php

namespace app\migrations;

use app\core\database\Migration;

class m2026_05_07_000230_add_event_language extends Migration
{

    public function up(): void
    {
        $this->db->exec("ALTER TABLE `event` ADD COLUMN `language` VARCHAR(20) NULL DEFAULT NULL AFTER `user_agent`;");
    }

    public function down(): void
    {
        $this->db->exec("ALTER TABLE `event` DROP COLUMN `language`;");
    }
    
}