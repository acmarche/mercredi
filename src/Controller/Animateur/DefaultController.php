<?php

namespace AcMarche\Mercredi\Controller\Animateur;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
final class DefaultController extends AbstractController
{
    public function __construct()
    {
        $organisationRepository->getOrganisation();
    }
    /**
     * @Route("/", name="mercredi_animateur_home")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function default()
    {
        return $this->render(
            '@AcMarcheMercrediAnimateur/default/index.html.twig',
            [
            ]
        );
    }
}
