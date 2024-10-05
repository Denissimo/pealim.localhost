<?php

namespace App\MessageHandler;

use App\Message\PealimParse;
use App\Service\Pealim;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PealimParseHandler
{
    private Pealim $pealimService;

    public function __construct(Pealim $pealimService)
    {
        $this->pealimService = $pealimService;
    }

    public function __invoke(PealimParse $message): void
    {
        $word = $message->getWord();
        $content = $this->pealimService->search($word);
        $cssClass = 'verb-search-result';
        $link = $this->pealimService->findLink($cssClass, $content);
        $isBaseExist = true;
        if (strlen($link)) {
            $isBaseExist = $this->pealimService->checkBase($link);
        } else {
            echo "\n$word Does not exist";
        }

        if (!$isBaseExist) {
            $wordFormContent = $this->pealimService->loadForms($link);
            $saved = $this->pealimService->parseForms($wordFormContent, $link);
            echo "\n$link: Saved $saved forms.";
        }else {
            echo "\n$word - Already saved";
        }
    }
}
