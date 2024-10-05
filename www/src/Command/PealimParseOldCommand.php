<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\Pealim;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use \Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Redis;


#[AsCommand(
    name: 'pealim:parse-old',
    description: 'Add a short description for your command',
)]
class PealimParseOldCommand extends Command
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
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mem1 = memory_get_usage();
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
//        $wordlist = 'לַעֲמוֹד,לאהוב,לַחשׁוֹב,להצטער,לְדַבֵּר,להבין,לָדַעַת,לִכְתּוֹב,לְבַקֵּר,לְהַרְגִּישׁ,לָלֶכֶת,לִנְסוֹעַ,לִקְרוֹא,לִשְׁמוֹר,לִמְכּוֹר,לְנַהֵל,לְהַנְדֵּס,לְהַפְקִיד,לִחְיוֹת,לֶאֱכוֹל,לִשְׁתּוֹת,לִרְצוֹת,לוּכַל ,ְהִישָּׁאֵר,לַחֲזֹר,לִהְיוֹת ,לְקַוּוֹת,לָלֶכֶת,לִנְסוֹעַ,לְהִימָּצֵא,לָבוֹא,לִישׁוֹן,לִסְגּוֹר,לְהַכִּיר,לוֹמַר,לְהַגִּיד,לִצְעוֹק,לְהַתְחִיל,לִפְתּוֹחַ,לִרְאוֹת,לְבַשֵּׁל,לְהָכִין,לְהַזְמִין,לִהְיוֹת,לְסַפֵּר,לְשַׂחֵק,לַעֲזוֹר,לִזְכּוֹר,לוֹקֵחַ,לְהַגִּיעַ,לְקַוּוֹת,לְקַבֵּל,לִשְׁלוֹחַ,לָתֵת,לִבְנוֹת,לְלַמֵּד,לְסַייֵּם,לִשְׁכּוֹחַ,לִכְעוֹס,לְהִישָּׁאֵר,לִתְפּוֹשׂ,לְהִתְחַתֵּן,לְהַסְכִּים,לְהִיפָּגֵשׁ,לִפְגּוֹשׁ,לַחֲזוֹר,לַחְזֹר,לִקְבּוֹעַ,לִרְקוֹד,לָשֶׁבֶת,לִגְמוֹר,לִשְׂכּוֹר,לַחְתּוֹם,לִמְסוֹר,לִבְדּוֹק,לְחַפֵּשׂ,לְבַקֵּשׁ,לְטַייֵּל,לְהַסְבִּיר,לְהַמְשִׁיךְ,לְהַכְנִיס,לְהִתְקַשֵּׁר,לְהִתְלַבֵּשׁ,לְהִתְפַּלֵּל,לְהִתְרַחֵץ,לְהִתְקַדֵּם,לְהִיכָּנֵס,לְהִיווָּלֵד,לִשְׁאוֹל,לְבַקֵר,לִשְׂמוֹחַ,לָקַחַת,לְכַבֵּס,לְצַלֵּם,לְפַטֵּר,לְטַפֵּל,לְנַגֵּן,לְתַקֵּן,לְסַדֵּר,לְהִשְׁתַּמֵּשׁ,לְתַרְגֵּם,טִיפַּלְתִּי,לְהִסְתַּכֵּל,הִסְתַּכַּלְתִּי,לִפְגּוֹעַ,לֶאֱסוֹר,לְהַשְׁפִּיעַ,לְהַמְלִיץ,הצטננתי,התחמם,התחתן,הצטלמתם,התרגשו,הזדקן,השתתפו,התקשר,מתקרר,להתכתב,להתקשר,להסתרר,להתפלל,להשתתף,להסתכל,להזדקן,להתנהג,להצטנן,להתקרם,להצטלם,להשתמש,התכתבן,התרחץ,התלבש,והסתרק,הסתכל,והתרגש,התנהג,והצטלמו,הצטער,והתכתבו,התחתנו,נרשמת,נכשלתי,נכרק,נגמרו,נפגשים,נזכרתי,נשארתי,להיפגש,להיבדק,להיסגר,להיתפס,להישלח,להיקלט,להיכשל,להיפרר,להירטב,להירשם,להיחתם,נפתחה,נבנס,נסגרה,נרטב,נכשל,ונשברה,נפרר,נחתם,נכתב,נשלח,נזכר,נפסק,נסגר';
//        $wordlist = 'לַעֲמוֹד,לאהוב,לַחשׁוֹב,להצטער,לְדַבֵּר,להבין,לָדַעַת,לִכְתּוֹב,לְבַקֵּר,לְהַרְגִּישׁ,לָלֶכֶת,לִנְסוֹעַ,לִקְרוֹא,לִשְׁמוֹר,לִמְכּוֹר,לְנַהֵל,לְהַנְדֵּס,לְהַפְקִיד,לִחְיוֹת,לֶאֱכוֹל,לִשְׁתּוֹת,לִרְצוֹת,לוּכַל ,ְהִישָּׁאֵר,לַחֲזֹר,לִהְיוֹת ,לְקַוּוֹת,לָלֶכֶת,לִנְסוֹעַ,לְהִימָּצֵא,לָבוֹא,לִישׁוֹן,לִסְגּוֹר,לְהַכִּיר,לוֹמַר,לְהַגִּיד,לִצְעוֹק,לְהַתְחִיל,לִפְתּוֹחַ,לִרְאוֹת,לְבַשֵּׁל,לְהָכִין,לְהַזְמִין,לִהְיוֹת,לְסַפֵּר,לְשַׂחֵק,לַעֲזוֹר,לִזְכּוֹר,לוֹקֵחַ,לְהַגִּיעַ,לְקַוּוֹת,לְקַבֵּל,לִשְׁלוֹחַ,לָתֵת,לִבְנוֹת,לְלַמֵּד,לְסַייֵּם,לִשְׁכּוֹחַ,לִכְעוֹס,לְהִישָּׁאֵר,לִתְפּוֹשׂ,לְהִתְחַתֵּן,לְהַסְכִּים,לְהִיפָּגֵשׁ,לִפְגּוֹשׁ,לַחֲזוֹר,לַחְזֹר,לִקְבּוֹעַ,לִרְקוֹד,לָשֶׁבֶת,לִגְמוֹר,לִשְׂכּוֹר,לַחְתּוֹם,לִמְסוֹר,לִבְדּוֹק,לְחַפֵּשׂ,לְבַקֵּשׁ,לְטַייֵּל,לְהַסְבִּיר,לְהַמְשִׁיךְ,לְהַכְנִיס,לְהִתְקַשֵּׁר,לְהִתְלַבֵּשׁ,לְהִתְפַּלֵּל,לְהִתְרַחֵץ,לְהִתְקַדֵּם,לְהִיכָּנֵס,לְהִיווָּלֵד';
//        $wordlist = 'לִשְׁאוֹל,לְבַקֵר,לִשְׂמוֹחַ,לָקַחַת,לְכַבֵּס,לְצַלֵּם,לְפַטֵּר,לְטַפֵּל,לְנַגֵּן,לְתַקֵּן,לְסַדֵּר,לְהִשְׁתַּמֵּשׁ,לְתַרְגֵּם,טִיפַּלְתִּי,לְהִסְתַּכֵּל,הִסְתַּכַּלְתִּי,לִפְגּוֹעַ,לֶאֱסוֹר,לְהַשְׁפִּיעַ,לְהַמְלִיץ,הצטננתי,התחמם,התחתן,הצטלמתם,התרגשו,הזדקן,השתתפו,התקשר,מתקרר,להתכתב,להתקשר,להסתרר,להתפלל,להשתתף,להסתכל,להזדקן,להתנהג,להצטנן,להתקרם,להצטלם,להשתמש,התכתבן,התרחץ,התלבש,והסתרק,הסתכל,והתרגש,התנהג,והצטלמו,הצטער,והתכתבו,התחתנו,נרשמת,נכשלתי,נכרק,נגמרו,נפגשים,נזכרתי,נשארתי,להיפגש,להיבדק,להיסגר,להיתפס,להישלח,להיקלט,להיכשל,להיפרר,להירטב,להירשם,להיחתם,נפתחה,נבנס,נסגרה,נרטב,נכשל,ונשברה,נפרר,נחתם,נכתב,נשלח,נזכר,נפסק,נסגר';
//        $wordlist = 'לַעֲמֹד';
//REDIS_URL=tcp://127.0.0.1:6379?database=0

//        $redis->connect('127.0.0.1', 6379);
//        $ans = $redis->ping();
        $client = RedisAdapter::createConnection('redis://pealim@host.docker.internal:6371');
        $z = $client->get('test01');
//        $client->set('test1', 'asdf');
//        $cache = new RedisTagAwareAdapter($client);
//        RedisAdapter::createConnection('redis://abcdef@localhost?timeout=5');

        $filesystem = new Filesystem();
//        $filesystem->mkdir(
//            Path::normalize('zzz_'.random_int(0, 1000)),
//        );
//        die;
        $fileContent = $filesystem->readFile('public/vocabulary.txt');
        $wordlist = explode("\r\n", $fileContent);
        $wordKeys = array_flip($wordlist);
        $lastWord = $client->get('last_word');
        $numWord = (int)$client->get('num_word');
        $lastIndex = $lastWord ? $wordKeys[$lastWord] : 0;
        $word = $wordlist[$lastIndex];
        $counter = 0;
//        foreach ($words as $word) {
        $counter++;
        $content = $this->pealimService->search($word);
        $cssClass = 'verb-search-result';
        $link = $this->pealimService->findLink($cssClass, $content);
        $content = null;
        unset($content);
        $isBaseExist = true;
        if (strlen($link)) {
            $isBaseExist = $this->pealimService->checkBase($link);
        } else {
            $io->error("$numWord ($word) Does not exist");
        }

        if (!$isBaseExist) {
            $wordFormContent = $this->pealimService->loadForms($link);
            $saved = $this->pealimService->parseForms($wordFormContent, $link);
            $wordFormContent = null;
            unset($wordFormContent);
            $numWord++;
            $mem2 = memory_get_usage();
            $io->writeln("$numWord) $link: Saved $saved forms. Memory: 1- $mem1; 2- $mem2;");
        }else {
            $io->warning("$numWord: $word - Already saved");
        }

//        }
        if (isset($wordlist[$lastIndex + 1])) {
            $client->set('last_word', $wordlist[$lastIndex + 1]);
            $client->set('num_word', $numWord);
            $vars = array_keys(get_defined_vars());
            foreach ($vars as $var) {
                if ($var != 'output') {
                    $$var = null;
                    unset($$var);
                }
            }

            unset($vars);

            $this->runAgain($output);
        } else {
            $client->del('last_word');
            $client->del('num_word');
        }
        $wordlist = null;
        unset($wordlist);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    private function runAgain(OutputInterface $output): void
    {
        $greetInput = new ArrayInput([
            'command' => 'pealim:parse'
        ]);

        $returnCode = $this->getApplication()->run($greetInput, $output);
    }
}
