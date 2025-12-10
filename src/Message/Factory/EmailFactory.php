<?php

namespace AcMarche\Mercredi\Message\Factory;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Mailer\TemplatedEmailFactory;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Vich\UploaderBundle\Storage\StorageInterface;

final class EmailFactory
{
    use OrganisationPropertyInitTrait;

    public function __construct(
        private StorageInterface $storage
    ) {
    }

    public function create(Message $message): TemplatedEmail
    {
        $notification = TemplatedEmailFactory::asPublicEmailJf();
        $notification->subject($message->getSujet())
            ->from($this->getEmailSenderAddress())
            ->htmlTemplate('@AcMarcheMercrediEmail/admin/mail.html.twig')
            ->context(
                [
                    'texte' => $message->getTexte(),
                    'organisation' => $this->organisation,
                ]
            );

        /**
         * Pieces jointes.
         */
        if (null !== ($uploadedFile = $message->getFile())) {
            $notification->attachFromPath(
                $uploadedFile->getRealPath(),
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientMimeType()
            );
        }

        return $notification;
    }

    public function createForPlaine(Message $message): TemplatedEmail
    {
        $notification = TemplatedEmailFactory::asPublicEmailJf();
        $notification->subject($message->getSujet())
            ->from($this->getEmailSenderAddress())
            ->htmlTemplate('@AcMarcheMercrediEmail/admin/mail.html.twig')
            ->context(
                [
                    'texte' => $message->getTexte(),
                    'organisation' => $this->organisation,
                ]
            );

        /**
         * Piece jointe.
         */
        if (null !== ($uploadedFile = $message->getFile())) {
            $notification->attachFromPath(
                $uploadedFile->getRealPath(),
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientMimeType()
            );
        }

        return $notification;
    }

    /**
     * @param TemplatedEmail $notification
     * @param array|PlaineGroupe[] $groupes
     * @return void
     */
    public function attachmentsForPlaine(TemplatedEmail $notification, array $groupes): void
    {
        foreach ($groupes as $plaineGroupe) {
            if ($plaineGroupe->getFileName()) {
                $path = $this->storage->resolvePath($plaineGroupe, 'file');
                $notification->attachFromPath(
                    $path,
                    $plaineGroupe->getGroupeScolaire()->getNom(),
                    $plaineGroupe->getMimeType()
                );
            }
        }
    }
}
