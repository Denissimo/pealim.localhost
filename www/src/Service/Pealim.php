<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpClient\HttpClient;
use \Symfony\Contracts\HttpClient\HttpClientInterface;

class Pealim
{
    private EntityManager $entityManager;

    private HttpClientInterface $client;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->client = HttpClient::create();
    }


}