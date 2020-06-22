<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
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
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;
    /**
     * @var RelationUtils
     */
    private $relationUtils;

    public function __construct(
        OrganisationRepository $organisationRepository,
        TuteurUtils $tuteurUtils,
        RelationUtils $relationUtils
    ) {
        $this->organisationRepository = $organisationRepository;
        $this->organisation = $organisationRepository->getOrganisation();
        $this->tuteurUtils = $tuteurUtils;
        $this->relationUtils = $relationUtils;
    }

    /**
     * @Route("/", name="mercredi_parent_home")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function default()
    {
        $user = $this->getUser();
        $tuteur = $this->tuteurUtils->getTuteurByUser($user);

        if (!$tuteur) {
            return $this->redirectToRoute('mercredi_parent_nouveau');
        }

        $enfants = $this->relationUtils->findEnfantsByTuteur($tuteur);
        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/default/index.html.twig',
            [
                'enfants' => $enfants,
                'tuteur' => $tuteur,
                'tuteurIsComplete' => $tuteurIsComplete,
                'year' => date('Y'),
            ]
        );
    }

    /**
     * @Route("/nouveau", name="mercredi_parent_nouveau")
     */
    public function nouveau()
    {
        return $this->render(
            '@AcMarcheMercrediParent/default/nouveau.html.twig',
            [
                'organisation' => $this->organisation,
            ]
        );
    }
}
