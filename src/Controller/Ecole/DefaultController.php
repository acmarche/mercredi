<?php

namespace AcMarche\Mercredi\Controller\Ecole;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    /**
     * @var Organisation|null
     */
    private $organisation;

    public function __construct(
        OrganisationRepository $organisationRepository
    ) {
        $this->organisationRepository = $organisationRepository;
        $this->organisation = $organisationRepository->getOrganisation();
    }

    /**
     * @Route("/", name="mercredi_ecole_home")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function default()
    {
        return $this->render(
            '@AcMarcheMercrediEcole/default/index.html.twig',
            [
            ]
        );
    }
}
