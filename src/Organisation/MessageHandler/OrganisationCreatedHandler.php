<?php


namespace AcMarche\Mercredi\Organisation\MessageHandler;


use AcMarche\Mercredi\Organisation\Message\OrganisationCreated;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OrganisationCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;

    public function __construct(OrganisationRepository $organisationRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->organisationRepository = $organisationRepository;
    }

    public function __invoke(OrganisationCreated $organisationCreated)
    {
        $this->flashBag->add('success', "L'organisation a bien été ajoutée");
    }

}
