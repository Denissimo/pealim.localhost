<?php

namespace App\Service\Unit;

class TableCell
{
    private string $content = '';

    private ?Word $word = null;

    private bool $isHeader = false;

    private string $class = '';

    private int $colspan = 1;

    private int $rowspan = 1;

    private int $x = 0;

    private int $y = 0;

    /**
     * @param string $content
     * @param Word|null $word
     * @param bool $isHeader
     * @param string $class
     * @param int $colspan
     * @param int $rowspan
     * @param int $x
     * @param int $y
     */
    public function __construct(string $content, ?Word $word = null, bool $isHeader = false, string $class = '', int $colspan = 1, int $rowspan = 1, int $x = 0, int $y = 0)
    {
        $this->content = $content;
        $this->word = $word;
        $this->isHeader = $isHeader;
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

    public function getWord(): ?Word
    {
        return $this->word;
    }

    public function isHeader(): bool
    {
        return $this->isHeader;
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

    public function setX(int $x): TableCell
    {
        $this->x = $x;

        return $this;
    }

    public function setY(int $y): TableCell
    {
        $this->y = $y;

        return $this;
    }
}