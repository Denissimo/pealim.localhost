<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;

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
//        $url = 'http://www.dostavka-buketov.ru/';

        $response = $this->client->request(
            'GET',
            $url
        );

        $statusCode = $response->getStatusCode();
        echo $statusCode . "\n";

        $contentType = $response->getHeaders()['content-type'][0];
        echo $contentType . "\n";

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
}