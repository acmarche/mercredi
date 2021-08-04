<?php

namespace AcMarche\Mercredi\Message\Factory;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Vich\UploaderBundle\Storage\StorageInterface;

final class EmailFactory
{
    use OrganisationPropertyInitTrait;

    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

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

        /**
         * Pieces jointes.
         */
        if (null !== ($uploadedFile = $message->getFile())) {
            $templatedEmail->attachFromPath(
                $uploadedFile->getRealPath(),
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientMimeType()
            );
        }

        return $templatedEmail;
    }

    public function createForPlaine(Plaine $plaine, Message $message): TemplatedEmail
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

        /**
         * Pieces jointes.
         */
        foreach ($plaine->getPlaineGroupes() as $plaineGroupe) {
            if($plaineGroupe->getFileName()){
                $path = $this->storage->resolvePath($plaineGroupe, 'file');
                $templatedEmail->attachFromPath(
                    $path,
                    $plaineGroupe->getGroupeScolaire()->getNom(),
                    $plaineGroupe->getMimeType()
                );
            }
        }

        return $templatedEmail;
    }
}
