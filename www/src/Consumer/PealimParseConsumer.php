<?php

namespace App\Consumer;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Input\ArrayInput;

class PealimParseConsumer implements ConsumerInterface
{
    private Application $application;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
    }

    /**
     * @var AMQPMessage $message
     * @return void
     */
    public function execute(AMQPMessage $message): void
    {
        $this->application->setAutoExit(false);

        $body = $message->getBody();

        $greetInput = new ArrayInput([
            'command' => $body['command'],
            'last_word' => $body['last_word'],
            'num_word' => $body['num_word']
        ]);

        $returnCode = $this->application->run($greetInput);
//        echo 'Ну тут типа сообщение пытаюсь отправить: '.$msg->getBody().PHP_EOL;
    }
}