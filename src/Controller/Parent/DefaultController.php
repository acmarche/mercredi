<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use AcMarche\Mercredi\Sante\Utils\SanteChecker;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class DefaultController extends AbstractController
{
    use GetTuteurTrait;
    use OrganisationPropertyInitTrait;

    public function __construct(
        private RelationUtils $relationUtils,
        private SanteChecker $santeChecker,
        private FactureRepository $factureRepository
    ) {
    }

    #[Route(path: '/', name: 'mercredi_parent_home')]
    #[IsGranted('ROLE_MERCREDI_PARENT')]
    public function default(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $this->santeChecker->isCompleteForEnfants($enfants);
        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($this->tuteur);
        $factures = $this->factureRepository->findFacturesByTuteurWhoIsSend($this->tuteur);

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

    #[Route(path: '/nouveau', name: 'mercredi_parent_nouveau')]
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
