<?php

namespace AcMarche\Mercredi\Controller\Parent;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
final class DefaultController extends AbstractController
{
    use GetTuteurTrait;
    use OrganisationPropertyInitTrait;

    private RelationUtils $relationUtils;
    private SanteChecker $santeChecker;
    private FactureRepository $factureRepository;

    public function __construct(
        RelationUtils $relationUtils,
        SanteChecker $santeChecker,
        FactureRepository $factureRepository
    ) {
        $this->relationUtils = $relationUtils;
        $this->santeChecker = $santeChecker;
        $this->factureRepository = $factureRepository;
    }

    /**
     * @Route("/", name="mercredi_parent_home")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function default(): Response
    {
        if (($t = $this->hasTuteur()) !== null) {
            return $t;
        }

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
    public function nouveau(): Response
    {
        return $this->render(
            '@AcMarcheMercrediParent/default/nouveau.html.twig',
            [
                'organisation' => $this->organisation,
            ]
        );
    }
}
