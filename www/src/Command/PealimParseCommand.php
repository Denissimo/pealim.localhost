<?php

namespace App\Command;

use App\Message\PealimParse;
use App\Service\Pealim;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'pealim:parse',
    description: 'Add a short description for your command',
)]
class PealimParseCommand extends Command
{
    private Pealim $pealimService;

    private MessageBusInterface $messageBus;

    public function __construct(Pealim $pealimService, MessageBusInterface $messageBus)
    {
        parent::__construct();
        $this->pealimService = $pealimService;
        $this->messageBus = $messageBus;
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

        $filesystem = new Filesystem();
        $fileContent = $filesystem->readFile('public/vocabulary.txt');
        $wordlist = explode("\r\n", $fileContent);

        foreach ($wordlist as $word) {
            $this->messageBus->dispatch(new PealimParse($word));
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
