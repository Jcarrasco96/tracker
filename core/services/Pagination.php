<?php

namespace app\core\services;

class Pagination
{

    public int $page;
    public int $pageSize;
    public int $total;

    public function __construct(int $page, int $pageSize, int $total)
    {
        $this->page = max(0, $page);
        $this->pageSize = $pageSize;
        $this->total = $total;
    }

    public function getPageCount(): int
    {
        return (int) ceil($this->total / $this->pageSize);
    }

    public function getBegin(): int
    {
        return $this->page * $this->pageSize + 1;
    }

    public function getEnd(): int
    {
        return min($this->total, ($this->page + 1) * $this->pageSize);
    }

}