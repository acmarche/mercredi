<?php


namespace AcMarche\Mercredi\Message\Factory;


use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;

class MessageFactory
{
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;

    public function __construct(OrganisationRepository $organisationRepository)
    {
        $this->organisationRepository = $organisationRepository;
    }

    public function createInstance(): Message
    {
        $organisation = $this->organisationRepository->getOrganisation();

        $message = new Message();
        $message->setFrom($organisation->getEmail());

        return $message;
    }
}
