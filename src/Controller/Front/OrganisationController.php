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
class OrganisationController extends AbstractController
{
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;

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
            '@AcMarcheMercredi/default/_organisation.html.twig',
            [
                'organisation' => $organisation,
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
            '@AcMarcheMercredi/default/_organisation_title.html.twig',
            [
                'organisation' => $organisation,
            ]
        );
    }
}
