<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
class DefaultController extends AbstractController
{
    use GetTuteurTrait;

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
    /**
     * @var SanteChecker
     */
    private $santeChecker;
    /**
     * @var FactureRepository
     */
    private $factureRepository;

    public function __construct(
        OrganisationRepository $organisationRepository,
        TuteurUtils $tuteurUtils,
        RelationUtils $relationUtils,
        SanteChecker $santeChecker,
        FactureRepository $factureRepository
    ) {
        $this->organisationRepository = $organisationRepository;
        $this->organisation = $organisationRepository->getOrganisation();
        $this->tuteurUtils = $tuteurUtils;
        $this->relationUtils = $relationUtils;
        $this->santeChecker = $santeChecker;
        $this->factureRepository = $factureRepository;
    }

    /**
     * @Route("/", name="mercredi_parent_home")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function default()
    {
        $this->hasTuteur();

        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $this->santeChecker->isCompleteForEnfants($enfants);
        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($this->tuteur);
        $factures = $this->factureRepository->findFacturesByTuteur($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/default/index.html.twig',
            [
                'enfants' => $enfants,
                'tuteur' => $this->tuteur,
                'factures' => $factures,
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
