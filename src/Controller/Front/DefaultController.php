<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
class DefaultController extends AbstractController
{
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;

    public function __construct(OrganisationRepository $organisationRepository)
    {
        $this->organisationRepository = $organisationRepository;
    }

    /**
     * @Route("/", name="mercredi_home")
     */
    public function default(Request $request)
    {
        return $this->render(
            '@AcMarcheMercredi/default/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/showorganisation", name="mercredi_show_organisation")
     */
    public function index()
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheMercredi/default/_organisation.html.twig',
            [
                'organisation' => $organisation,
            ]
        );
    }
}
