<?php

namespace App\Message;

final class PealimParse
{
    private string $word;

    /**
     * @param string $word
     */
    public function __construct(string $word)
    {
        $this->word = $word;
    }

    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->word;
    }
}
