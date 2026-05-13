<?php

declare(strict_types=1);

namespace app\migrations;

use app\core\database\Migration;

class m2026_05_06_184406_event extends Migration
{

    public function up(): void
    {
        $this->db->exec("CREATE TABLE `event` (
            `id` char(36) NOT NULL,
            `website_id` char(36) NOT NULL,
            `event_type` VARCHAR(10) NOT NULL DEFAULT '',
            `url` VARCHAR(200) NOT NULL DEFAULT '',
            `referrer` VARCHAR(200) NOT NULL DEFAULT '',
            `user_agent` VARCHAR(200) NOT NULL DEFAULT '',
            `ip_hash` VARCHAR(100) NOT NULL DEFAULT '',
            `created_at` DATETIME NOT NULL DEFAULT current_timestamp(),
            `label` VARCHAR(255) NULL DEFAULT NULL,
            `value` VARCHAR(255) NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

        $this->db->exec("ALTER TABLE `event`
            ADD PRIMARY KEY (`id`),
            ADD INDEX `FK__website` (`website_id`) USING BTREE,
            ADD CONSTRAINT `FK__website` FOREIGN KEY (`website_id`) REFERENCES `website` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE `event`;");
    }
    
}