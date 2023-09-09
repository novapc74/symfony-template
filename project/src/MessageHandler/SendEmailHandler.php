<?php

namespace App\MessageHandler;

use App\Message\SendEmail;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendEmailHandler
{
    public function __invoke(SendEmail $sendEmail): void
    {
        $sendEmail->sendEmail();
    }
}

/*

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;

class DefaultController extends AbstractController
{
    public function index(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new SendEmail($feedback));
    }
}


*/