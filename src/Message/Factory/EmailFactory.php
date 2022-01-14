<?php

namespace AcMarche\Mercredi\Message\Factory;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Vich\UploaderBundle\Storage\StorageInterface;

final class EmailFactory
{
    use OrganisationPropertyInitTrait;

    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function create(Message $message): NotificationEmail
    {
        $notification = NotificationEmailJf::asPublicEmailJf();
        $notification->subject($message->getSujet())
            ->from($message->getFrom())
            ->htmlTemplate('@AcMarcheMercrediEmail/admin/mail.html.twig')
            ->context(
                [
                    'texte' => $message->getTexte(),
                    'organisation' => $this->organisation,
                ]
            );

        /*
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

    public function createForPlaine(Plaine $plaine, Message $message, bool $attachCourriers): NotificationEmail
    {
        $notification = NotificationEmailJf::asPublicEmailJf();
        $notification->subject($message->getSujet())
            ->from($message->getFrom())
            ->htmlTemplate('@AcMarcheMercrediAdmin/admin/mail.html.twig')
            ->context(
                [
                    'texte' => $message->getTexte(),
                    'organisation' => $this->organisation,
                ]
            );

        /*
         * Pieces jointes.
         */
        if ($attachCourriers) {
            foreach ($plaine->getPlaineGroupes() as $plaineGroupe) {
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

        return $notification;
    }
}
