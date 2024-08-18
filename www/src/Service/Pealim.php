<?php

namespace App\Service;

use App\Entity\PealimBase;
use App\Service\Unit\TableCell;
use App\Service\Unit\Verb;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Unit\Word;
use App\Entity\PealimVocabulary;

class Pealim
{
    private const ROWS_NORMAL = 9;
    private EntityManagerInterface $entityManager;

    private HttpClientInterface $client;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->client = HttpClient::create();
    }

    public function search(string $word)
    {
        $url = 'https://www.pealim.com/ru/search/?from-nav=1&q=' . $word;

        return $this->grabByUrl($url);
    }

    public function loadForms(string $path)
    {
        $url = 'https://www.pealim.com' . $path;

        return $this->grabByUrl($url);
    }

    private function grabByUrl(string $url)
    {
        $response = $this->client->request(
            'GET',
            $url
        );

        $statusCode = $response->getStatusCode();
//        echo $statusCode . "\n";

        $contentType = $response->getHeaders()['content-type'][0] ?? null;
//        echo $contentType . "\n";

        $content = $response->getContent();
//        echo $content . "\n";
//die;
//        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;

    }

    public function findLink(string $cssClass, string $text): string
    {
        //<div class="verb-search-result" onclick="javascript:window.document.location=&quot;/ru/dict/7-lalechet/&quot;">
        $template = '/<div class=\"verb-search-result\"[^\/]+(\/ru\/dict\/[a-zA-Z0-9\-_]+\/)/';
        preg_match($template, $text, $matches);

        return $matches[1] ?? '';
    }

    public function parseForms(string $content, ?string $path = null): int
    {

//        $tplPart = '/<\/h2><p>([А-Яа-яA-Z-a-z0-9]+)[^<]+<b>[^<]+<\/b>.+<\/h3><div class="lead">([А-Яа-яA-Z-a-z0-9]+)</';
        $tplPart = '/<\/h2><p>([А-Яа-яA-Z-a-z0-9]+)[^<]+<b>([^<]+)<\/b><\/p><p>Корень: <span[^>]+><a[^>]+>([^<]+)<\/a><\/span>/';
        $tplRus = '/<div class="lead">([^<]+)<\/div>/';
        preg_match_all($tplPart, $content, $parts);
        preg_match_all($tplRus, $content, $rus);
        $speechPart = $parts[1][0] ?? '';
        $form = $parts[2][0] ?? '';
        $root = $parts[3][0] ?? '';
        $translation = $rus[1][0] ?? '';
        $pealimBase = (new PealimBase())->setSlug($path)
            ->setForm($form)
            ->setSpeechPart($speechPart)
            ->setTranslation($translation)
            ->setRoot($root);
        ;

        $this->entityManager->persist($pealimBase);
        $this->entityManager->flush();

        $table = $this->parseTable($content);
        $head = $this->parseHead($table);
        $body = $this->parseBody($table);
//        $bodyReplaced = preg_replace(['/<th/', '/<\/th/'], ['<td', '</td'], $body);
        $headTr = $this->parseTr($head);
        $bodyTr = $this->parseTr($body);
        $maxColumns = 0;
        foreach ($headTr as $level => $tr) {
            $td = $this->parseTd($tr, $level);
            $columns = count($td);
            $maxColumns = $columns > $maxColumns ? $columns : $maxColumns;
            $headTd[] = $td;
        }
        $bodyTd = [];
        $rowsDiff = self::ROWS_NORMAL - count($bodyTr);
        foreach ($bodyTr as $level => $tr) {
            $td = $this->parseTd($tr, $level + $rowsDiff);
            $columns = count($td);
            $maxColumns = max($columns, $maxColumns);
            $bodyTd[] = $td;
        }

        $cellsTable = [];

        foreach ($bodyTd as $level => $cellsList) {
            foreach ($cellsList as $cell) {
                if ($cell->isHeader()) {
                    continue;
                }
                $cellsTable[] = $cell;
                if (Verb::getTime($level) == Verb::INFINITIVE) {
                    continue;
                }
//                $colspan = $cell->getColSpan();
//                $rowspan = $cell->getRowspan();
//                for ($i = 1; $i < $colspan; $i++) {
//                    $newCell = clone $cell;
//                    $x = $newCell->getX();
//                    $newCell->setX($x + $i);
//                    $cellsTable[] = $newCell;
//                }
//                for ($k = 1; $k < $rowspan; $k++) {
//                    $newCell = clone $cell;
//                    $y = $newCell->getY();
//                    $newCell->setY($y + $k);
//                    $cellsTable[] = $newCell;
//                }
            }
        }

        foreach ($cellsTable as $cell) {
            $time = Verb::getTime($cell->getY());
            $isPlural = $time == Verb::INFINITIVE ? null : Verb::isPlural($cell);
            $person = $time == Verb::INFINITIVE || $time == Verb::TIME_PRESENT ? null : Verb::getPerson($cell->getY());
            $isMasculine = $person == 1 || $time == Verb::INFINITIVE ? null : Verb::isMasculine($cell->getX());
            $word = $cell->getWord()->getHebrew();
            $transcription = $cell->getWord()->getTranscription();

            $vocabularyUnit = (new PealimVocabulary())
                ->setPealimBase($pealimBase)
                ->setWord($word)
                ->setTranscription($transcription)
                ->setMasculine($isMasculine)
                ->setPlural($isPlural)
                ->setTime($time)
                ->setPerson($person);

            $this->entityManager->persist($vocabularyUnit);
        }

        $this->entityManager->flush();

        $vars = array_keys(get_defined_vars());
        for ($i = 0; $i < sizeOf($vars); $i++) {
            if ($vars[$i]=='cellsTable') {
                continue;
            }
            $varName = $vars[$i];
            unset($$varName);
        }
        unset($vars,$i, $varName);

        return count($cellsTable);
    }

    public function checkBase(string $slug): bool
    {
        return $this->entityManager
                ->getRepository(PealimBase::class)
                ->findOneBy(['slug' => $slug]) instanceof PealimBase;
    }

    private function parseTable(string $content, string $class = 'table table-condensed conjugation-table'): string
    {
        $tplTable = '/<table class="' . $class . '">(.+)<\/table>/U';
        preg_match_all($tplTable, $content, $table);

        return $table[1][0] ?? '';
    }

    private function parseHead(string $content): string
    {
        $tplThead = '/<thead[^>]*>(.+)<\/thead>/U';
        preg_match_all($tplThead, $content, $head);

        return $head[1][0] ?? '';
    }

    private function parseBody(string $content): string
    {
        $tplTbody = '/<tbody[^>]*>(.+)<\/tbody>/U';
        preg_match_all($tplTbody, $content, $body);

        return $body[1][0] ?? '';
    }

    private function parseTr(string $content): array
    {
        $tplTr = '/<tr[^>]*>(.+)<\/tr>/U';
        preg_match_all($tplTr, $content, $tr);

        return $tr[1] ?? [];
    }

    /**
     * @param string $content
     * @param int $level
     *
     * @return TableCell[]|array
     */
    private function parseTd(string $content, int $level = 0): array
    {
        $tplTd = '/<(td|th)( class="([A-Za-z\-_]+)")?( rowspan="([0-9]+)")?( colspan="([0-9]+)")?>(.+)<\/(td|th)>/U';
        preg_match_all($tplTd, $content, $td);
        $cells = count((array)current($td));
        $tableCells = [];
        $xCoord = 0;
        for ($i = 0; $i < $cells; $i++) {
            $currentCell = array_column($td, $i);
            $cellContent = $currentCell[8] ?? '';
            $word = null;
            $isHeader = isset($currentCell[1]) && $currentCell[1] == 'th';
            if (!$isHeader) {
                $word = new Word($cellContent);
                $xCoord++;
            }
            $colspan = (int)$currentCell[7] ?? 1;
            $rowspan = (int)$currentCell[5] ?? 1;
            $tableCells[] = new TableCell(
                $cellContent,
                $word,
                $isHeader,
                $currentCell[3] ?? '',
                $colspan ?: 1,
                $rowspan ?: 1,
                $xCoord,
                $level
            );
        }

        return $tableCells;
    }
}