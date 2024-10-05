<?php

namespace App\MessageHandler;

use App\Message\PealimParse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PealimParseHandler
{
    public function __invoke(PealimParse $message): void
    {
        // do something with your message
        $word = $message->getWord();
        echo "\n$word\n";
    }
}
