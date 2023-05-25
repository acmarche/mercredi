<?php

namespace AcMarche\Mercredi\Organisation\MessageHandler;

use AcMarche\Mercredi\Organisation\Message\OrganisationCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class OrganisationCreatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(OrganisationCreated $organisationCreated): void
    {
        $this->flashBag->add('success', "L'organisation a bien été ajoutée");
    }
}
