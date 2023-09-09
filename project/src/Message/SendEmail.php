<?php

namespace App\Message;


use App\Service\MailerService;

class SendEmail
{
    public function __construct(private readonly MailerService $mailerService, private /*Feedback*/ $feedback)
    {
    }

    public function sendEmail(): void
    {
        $this->mailerService->resolveMailer($this->feedback);
    }
}