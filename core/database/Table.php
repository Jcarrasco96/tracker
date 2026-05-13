<?php

declare(strict_types=1);

namespace app\core\database;

final class Table
{

    public string $name;
    private array $columns = [];
    private array $primaryKeys = [];
    private array $uniqueKeys = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getPrimaryKeys(): array
    {
        return $this->primaryKeys;
    }

    public function getUniqueKeys(): array
    {
        return $this->uniqueKeys;
    }

    public function uuid(string $column, array $options = []): self
    {
        $this->columns[] = $this->columnDefinition($column, 'CHAR(36)', $options);
        return $this;
    }

    public function string(string $column, int $length = 255, array $options = []): self
    {
        $this->columns[] = $this->columnDefinition($column, "VARCHAR($length)", $options);
        return $this;
    }

    public function integer(string $column, array $options = []): self
    {
        $this->columns[] = $this->columnDefinition($column, 'INT', $options);
        return $this;
    }

    public function primary(array|string $columns): self
    {
        $cols = (array) $columns;
        $this->primaryKeys = array_merge($this->primaryKeys, $cols);
        return $this;
    }

    public function unique(string $name, array|string $columns): self
    {
        $cols = (array) $columns;
        $this->uniqueKeys[$name] = "UNIQUE KEY `$name` (" . $this->quoteColumns($cols) . ")";
        return $this;
    }

    public function index(string $name, array|string $columns): self
    {
        $cols = (array) $columns;
        $this->uniqueKeys[$name] = "KEY `$name` (" . $this->quoteColumns($cols) . ")";
        return $this;
    }

    private function columnDefinition(string $name, string $type, array $options): string
    {
        $defs = "`$name` $type";

        $defs .= ($options['null'] ?? false) ? ' NULL' : ' NOT NULL';

        if (array_key_exists('default', $options)) {
            $def = $options['default'];
            $defs .= ' DEFAULT ' . (is_string($def) ? $this->quote($def) : $def);
        }

        if (!empty($options['auto_increment'])) {
            $defs .= ' AUTO_INCREMENT';
        }

        if (!empty($options['comment'])) {
            $defs .= ' COMMENT ' . $this->quote($options['comment']);
        }

        return $defs;
    }

    private function quote(string $value): string
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }

    private function quoteColumns(array $cols): string
    {
        return implode(', ', array_map(fn($c) => "`$c`", $cols));
    }

}