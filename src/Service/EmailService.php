<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $body): void
    {
        $email = (new Email())
            ->from('agrebaouiyoussef123@gmail.com') // Votre adresse Gmail
            ->to($to)
            ->subject($subject)
            ->text($body)
            ->html('<p>' . $body . '</p>'); // Optionnel : version HTML

        $this->mailer->send($email);
    }
}