<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Facture\FactureRenderInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('ROLE_MERCREDI_PARENT')]
#[Route(path: '/facture')]
final class FactureController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private FactureRepository $factureRepository,
        private FacturePresenceRepository $facturePresenceRepository,
        private FactureCalculatorInterface $factureCalculator,
        private factureRenderInterface $factureRender,
    ) {}

    #[Route(path: '/', name: 'mercredi_parent_facture_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $factures = $this->factureRepository->findFacturesByTuteurWhoIsSend($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $this->tuteur,
            ],
        );
    }

    #[Route(path: '/{uuid}/show', name: 'mercredi_parent_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $html = $this->factureRender->render($facture);

        return $this->render(
            '@AcMarcheMercrediParent/facture/show.html.twig',
            [
                'facture' => $facture,
                'content' => $html,
            ],
        );
    }
}
