<?php

namespace AcMarche\Mercredi\Controller\Front;

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
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;
    /**
     * @var string
     */
    private const ORGANISATION = 'organisation';

    public function __construct(
        OrganisationRepository $organisationRepository
    ) {
        $this->organisationRepository = $organisationRepository;
    }

    /**
     * @Route("/show", name="mercredi_organisation_show")
     */
    public function organisation()
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
    public function organisationTitle()
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
    public function organisationShort()
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
