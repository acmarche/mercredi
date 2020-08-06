<?php

namespace AcMarche\Mercredi\Message\Factory;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class EmailFactory
{
    use OrganisationPropertyInitTrait;

    public function create(Message $message): TemplatedEmail
    {
        $templatedEmail = (new TemplatedEmail())
            ->subject($message->getSujet())
            ->from($message->getFrom())
            //  ->htmlTemplate('@AcMarcheMercrediAdmin/mail/mail.html.twig')
            ->textTemplate('@AcMarcheMercrediAdmin/message/_mail.txt.twig')
            ->context(
                [
                    'texte' => $message->getTexte(),
                    'organisation' => $this->organisation,
                ]
            );

        /*
         * Pieces jointes.
         */
        if (($uploadedFile = $message->getFile()) !== null) {
            $templatedEmail->attachFromPath(
                $uploadedFile->getRealPath(),
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientMimeType()
            );
        }

        return $templatedEmail;
    }
}
