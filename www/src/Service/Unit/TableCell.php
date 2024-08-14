<?php

namespace App\Service\Unit;

class TableCell
{
    private string $content = '';

    private string $class = '';

    private int $colspan = 1;

    private int $rowspan = 1;

    private int $x = 0;

    private int $y = 0;

    /**
     * @param string $content
     * @param int $colspan
     * @param int $rowspan
     * @param int $x
     * @param int $y
     */
    public function __construct(string $content, string $class = '', int $colspan = 1, int $rowspan = 1, int $x = 0, int $y = 0)
    {
        $this->content = $content;
        $this->class = $class;
        $this->colspan = $colspan;
        $this->rowspan = $rowspan;
        $this->x = $x;
        $this->y = $y;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getColspan(): int
    {
        return $this->colspan;
    }

    public function getRowspan(): int
    {
        return $this->rowspan;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }
}