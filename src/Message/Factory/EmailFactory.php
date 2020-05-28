<?php


namespace AcMarche\Mercredi\Message\Factory;


use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailFactory
{
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;
    /**
     * @var Organisation|null
     */
    private $organisation;

    public function __construct(OrganisationRepository $organisationRepository)
    {
        $this->organisationRepository = $organisationRepository;
        $this->organisation = $organisationRepository->getOrganisation();
    }

    public function create(Message $message): TemplatedEmail
    {
        $email = (new TemplatedEmail())
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
        if ($file = $message->getFile()) {
            $email->attachFromPath($file->getRealPath(), $file->getClientOriginalName(), $file->getClientMimeType());
        }

        return $email;
    }
}
