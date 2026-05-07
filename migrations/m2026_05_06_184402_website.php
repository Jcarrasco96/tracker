<?php

namespace app\migrations;

use app\core\database\Migration;

class m2026_05_06_184402_website extends Migration
{

    public function up(): void
    {
        $this->db->exec("CREATE TABLE `website` (
            `id` char(36) NOT NULL,
            `domain` varchar(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

        $this->db->exec("ALTER TABLE `website`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `domain` (`domain`);");
    }

    public function down(): void
    {
        $this->db->exec("DROP TABLE `website`;");
    }
    
}