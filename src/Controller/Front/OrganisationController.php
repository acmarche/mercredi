<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/organisation')]
final class OrganisationController extends AbstractController
{
    public function __construct(
        private OrganisationRepository $organisationRepository,
    ) {}

    #[Route(path: '/show', name: 'mercredi_organisation_show')]
    public function organisation(): Response
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercredi/organisation/_organisation.html.twig',
            [
                'organisation' => $organisation,
            ],
        );
    }

    #[Route(path: '/title', name: 'mercredi_organisation_title')]
    public function organisationTitle(): Response
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercredi/organisation/_organisation_title.html.twig',
            [
                'organisation' => $organisation,
            ],
        );
    }

    #[Route(path: '/short', name: 'mercredi_organisation_short')]
    public function organisationShort(): Response
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercredi/organisation/_organisation_short.html.twig',
            [
                'organisation' => $organisation,
            ],
        );
    }
}
