<?php

namespace app\core\database;

use PDO;

abstract class Migration
{

    public PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    abstract public function up();

    abstract public function down();

    public function insert(string $table, array $data): void
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO $table ($columns) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
    }

}