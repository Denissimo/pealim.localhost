<?php

namespace App\Command;

use App\Service\Pealim;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pealim:word',
    description: 'Add a short description for your command',
)]
class ParseWordCommand extends Command
{
    private Pealim $pealimService;

    public function __construct(Pealim $pealimService)
    {
        parent::__construct();
        $this->pealimService = $pealimService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('words', InputArgument::IS_ARRAY, 'Words', ['רָזֶה'])
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $words = $input->getArgument('words');

        if ($words) {
//            $io->note(sprintf('You passed an argument: %s', $word));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        foreach ($words as $word) {
            $content = $this->pealimService->search($word);
            $cssClass = 'verb-search-result';
            $links = $this->pealimService->findLink($cssClass, $content);
            foreach ($links as $link) {
                $isBaseExist = true;
                if (strlen($link)) {
                    $isBaseExist = $this->pealimService->checkBase($link);
                } else {
                    echo "\n$word exist";
                }

                if (!$isBaseExist) {
                    $wordFormContent = $this->pealimService->loadForms($link);
                    $saved = $this->pealimService->parseForms($wordFormContent, $link);
                    echo "\n$link: Saved $saved forms.";
                } else {
                    echo "\n$word - Already saved";
                }
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
