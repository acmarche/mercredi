<?php

namespace AcMarche\Mercredi\Organisation\MessageHandler;

use AcMarche\Mercredi\Organisation\Message\OrganisationUpdated;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OrganisationUpdatedHandler implements MessageHandlerInterface
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

    public function __invoke(OrganisationUpdated $organisationUpdated): void
    {
        $this->flashBag->add('success', "L'organisation a bien été modifiée");
    }
}
