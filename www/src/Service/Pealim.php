<?php

namespace App\Service;

use App\Service\Unit\TableCell;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Unit\Word;
use App\Entity\PealimVocabulary;

class Pealim
{
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

    public function parseForms(string $content)
    {
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
            $maxColumns =  $columns > $maxColumns ? $columns : $maxColumns;
            $headTd[] = $td;
        }
        foreach ($bodyTr as $level => $tr) {
            $td = $this->parseTd($tr, $level);
            $columns = count($td);
            $maxColumns =  $columns > $maxColumns ? $columns : $maxColumns;
            $bodyTd[] = $td;
        }
        
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

    private function parseTd(string $content, $level = 0): array
    {
        $tplTd = '/<(td|th)( class="([A-Za-z\-_]+)")?( rowspan="([0-9]+)")?( colspan="([0-9]+)")?>(.+)<\/(td|th)>/U';
        preg_match_all($tplTd, $content, $td);
        $cells = count((array)current($td));
        $tableCells = [];
        $xCoord = 0;
        for ($i = 0; $i < $cells; ++$i) {
            $currentCell = array_column($td, $i);
            $content = $currentCell[8] ?? '';
            $word = null;
            $isHeader = isset($currentCell[1]) && $currentCell[1] == 'th';
            if (!$isHeader) {
                $word = new Word($content);
                $xCoord++;
            }
            $colspan = (int)$currentCell[7] ?? 0;
            $rowspan = (int)$currentCell[5] ?? 0;
            $tableCells[] = new TableCell(
                $content,
                $word,
                $isHeader,
                $currentCell[3] ?? '',
                $colspan,
                $rowspan,
                $xCoord++,
                $level
            );
        }

        return $tableCells;
    }
}