<?php

namespace App\Message;

final class TestMessage
{
    private string $message;

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
