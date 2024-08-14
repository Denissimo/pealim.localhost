<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\Pealim;

#[AsCommand(
    name: 'pealim:parse',
    description: 'Add a short description for your command',
)]
class PealimParseCommand extends Command
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
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $content = $this->pealimService->search('לָלֶכֶת');
        $cssClass = 'verb-search-result';
        $link =  $this->pealimService->findLink($cssClass, $content);
        $wordFormContent =  $this->pealimService->loadForms($link);
        $saved = $this->pealimService->parseForms($wordFormContent, $link);
        $io->info("Saved $saved forms");

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
