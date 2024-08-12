<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PealimVocabulary as Word;

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
        $template = '/<\/h2>/s';
    }
}