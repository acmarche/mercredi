<?php

namespace AcMarche\Mercredi\Controller\Front;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrganisationController.
 *
 * @Route("/organisation")
 */
final class OrganisationController extends AbstractController
{
    private const ORGANISATION = 'organisation';
    private OrganisationRepository $organisationRepository;

    public function __construct(
        OrganisationRepository $organisationRepository
    ) {
        $this->organisationRepository = $organisationRepository;
    }

    /**
     * @Route("/show", name="mercredi_organisation_show")
     */
    public function organisation(): Response
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercredi/organisation/_organisation.html.twig',
            [
                self::ORGANISATION => $organisation,
            ]
        );
    }

    /**
     * @Route("/title", name="mercredi_organisation_title")
     */
    public function organisationTitle(): Response
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercredi/organisation/_organisation_title.html.twig',
            [
                self::ORGANISATION => $organisation,
            ]
        );
    }

    /**
     * @Route("/short", name="mercredi_organisation_short")
     */
    public function organisationShort(): Response
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercredi/organisation/_organisation_short.html.twig',
            [
                self::ORGANISATION => $organisation,
            ]
        );
    }
}
