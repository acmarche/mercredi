<?php

namespace AcMarche\Mercredi\Mailer;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

trait InitMailerTrait
{
    private MailerInterface $mailer;

    /**
     * @required
     */
    public function setMailer(MailerInterface $mailer): void
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMail(Email $email): void
    {
        $this->mailer->send($email);
    }
}
