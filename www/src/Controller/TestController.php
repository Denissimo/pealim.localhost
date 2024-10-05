<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Message\PealimParse;
use Symfony\Component\Messenger\MessageBusInterface;

class TestController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('/test', name: 'app_test')]
    public function index(MessageBusInterface $bus): Response
    {
        $a = 12;
//        $bus->dispatch(new TestMessage('testt_' . rand(10,99)));
        $bus->dispatch(new PealimParse('pealim_' . rand(10,99)));

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
