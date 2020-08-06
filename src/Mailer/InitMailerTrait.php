<?php


namespace AcMarche\Mercredi\Mailer;


use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

trait InitMailerTrait
{
    use OrganisationPropertyInitTrait;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @param MailerInterface $mailer
     * @required
     */
    public function setMailer(
        MailerInterface $mailer
    ) {
        $this->mailer = $mailer;
    }

    /**
     * @param Email $email
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendMail(Email $email): void
    {
        $this->mailer->send($email);
    }
}
